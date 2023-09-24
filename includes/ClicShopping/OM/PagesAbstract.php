<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use ReflectionClass;
use function array_slice;
use function count;
use function in_array;
use function is_array;

abstract class PagesAbstract implements \ClicShopping\OM\PagesInterface
{
  public array $data = [];

  protected string $code;
  protected $file = 'main.php';
  protected bool $use_site_template = true;
  protected SitesInterface $site;
  protected array $actions_run = [];
  protected array $ignored_actions = [];
  protected bool $is_rpc = false;

  protected mixed $app;

  final public function __construct(\ClicShopping\OM\SitesInterface $site)
  {
    $this->code = (new ReflectionClass($this))->getShortName();
    $this->site = $site;

    $this->init();
  }

  protected function init()
  {
  }

  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * @return false|string
   * @throws \ReflectionException
   */
  public function getFile()
  {
    if (isset($this->file)) {
      return dirname(CLICSHOPPING::BASE_DIR) . '/' . str_replace('\\', '/', (new ReflectionClass($this))->getNamespaceName()) . '/templates/' . $this->file;
    } else {
      return false;
    }
  }

  /**
   * @param bool $bool
   */
  public function setUseSiteTemplate(bool $bool)
  {
    $this->use_site_template = ($bool === true);
  }

  /**
   * @return bool
   */
  public function useSiteTemplate(): bool
  {
    return $this->use_site_template;
  }

  /**
   * @param $file
   */
  public function setFile($file)
  {
    $this->file = $file;
  }

  /**
   * @return bool
   */
  public function isActionRequest(): bool
  {
    $furious_pete = [];

    if (count($_GET) > $this->site->actions_index) {
      $furious_pete = array_keys(array_slice($_GET, $this->site->actions_index, null, true));
    }

    if (!empty($furious_pete)) {
      $action = HTML::sanitize(basename($furious_pete[0]));

      if (!in_array($action, $this->ignored_actions, true) && $this->actionExists($action)) {
        return true;
      }
    }

    return false;
  }

  /**
   * @param $actions
   */
  public function runAction($actions)
  {
    if (!is_array($actions)) {
      $actions = [
        $actions
      ];
    }

    $run = [];

    foreach ($actions as $action) {
      $run[] = $action;

      if ($this->actionExists($run)) {
        $this->actions_run[] = $action;

        $class = $this->getActionClassName($run);

        $ns = explode('\\', $class);

        if ((count($ns) > 2) && ($ns[0] == 'ClicShopping') && ($ns[1] == 'Apps')) {
          if (isset($this->app) && is_subclass_of($this->app, 'ClicShopping\OM\AppAbstract')) {
            if ($this->app->definitionsExist(implode('/', array_slice($ns, 4)))) {
              $this->app->loadDefinitions(implode('/', array_slice($ns, 4)));
            }
          }
        }

        $action = new $class($this);

        $action->execute();

        if ($action->isRPC()) {
          $this->is_rpc = true;
        }
      } else {
        break;
      }
    }
  }

  /**
   *
   */
  public function runActions()
  {
    $actions = $furious_pete = [];

    if (count($_GET) > $this->site->actions_index) {
      $furious_pete = array_keys(array_slice($_GET, $this->site->actions_index, null, true));
    }

    foreach ($furious_pete as $action) {
      $action = HTML::sanitize(basename($action));

      $actions[] = $action;

      if (in_array($action, $this->ignored_actions, true) || !$this->actionExists($actions)) {
        array_pop($actions);

        break;
      }
    }

    if (!empty($actions)) {
      $this->runAction($actions);
    }
  }

  /**
   * @param $action
   * @return bool
   */
  public function actionExists($action)
  {
    if (!is_array($action)) {
      $action = [
        $action
      ];
    }

    $class = $this->getActionClassName($action);

    if (class_exists($class)) {
      if (is_subclass_of($class, 'ClicShopping\OM\PagesActionsInterface')) {
        return true;
      } else {
        trigger_error('ClicShopping\OM\PagesAbstract::actionExists() - ' . implode('\\', $action) . ': Action does not implement ClicShopping\OM\PagesActionInterface and cannot be loaded.');
      }
    }

    return false;
  }

  /**
   * @return array
   */
  public function getActionsRun()
  {
    return $this->actions_run;
  }

  /**
   * @return bool
   */
  public function isRPC(): bool
  {
    return ($this->is_rpc === true);
  }

  /**
   * @param $action
   * @return string
   * @throws ReflectionException
   */
  protected function getActionClassName($action)
  {
    if (!is_array($action)) {
      $action = [
        $action
      ];
    }

    return (new ReflectionClass($this))->getNamespaceName() . '\\Actions\\' . implode('\\', $action);
  }
}
