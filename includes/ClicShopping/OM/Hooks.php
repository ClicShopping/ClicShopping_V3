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

use DirectoryIterator;
use ReflectionFunction;
use function call_user_func_array;
use function is_string;

class Hooks
{
  protected ?string $site;
  protected array $hooks = [];
  protected array $watches = [];

  public function __construct(?string $site = null)
  {
    if (!isset($site)) {
      $site = CLICSHOPPING::getSite();
    }

    $this->site = basename($site);
  }

  /**
   * @param string $group
   * @param string $hook
   * @param array|null $parameters
   * @param string|null $action
   * @return array
   * @throws \ReflectionException
   */
  public function call(string $group, string $hook, ?array $parameters = null, ?string $action = null): array
  {
    if (!isset($action)) {
      $action = 'execute';
    }

    if (!isset($this->hooks[$this->site][$group][$hook][$action])) {
      $this->register($group, $hook, $action);
    }

    $calls = [];

    if (isset($this->hooks[$this->site][$group][$hook][$action])) {
      $calls = $this->hooks[$this->site][$group][$hook][$action];
    }

    if (isset($this->watches[$this->site][$group][$hook][$action])) {
      $calls = array_merge($calls, $this->watches[$this->site][$group][$hook][$action]);
    }

    $result = [];

    foreach ($calls as $code) {
      $bait = null;

      if (is_string($code)) {
        $class = Apps::getModuleClass($code, 'Hooks');

        $obj = new $class();

        $bait = $obj->$action($parameters);
      } else {
        $ref = new ReflectionFunction($code);

        if ($ref->isClosure()) {
          $bait = $code($parameters);
        }
      }

      if (!empty($bait)) {
        $result[] = $bait;
      }
    }

    return $result;
  }

  /**
   * @return string
   */
  public function output(): string
  {
    return implode('', call_user_func_array([$this, 'call'], \func_get_args()));
  }

  /**
   * @param string $group
   * @param string $hook
   * @param string $action
   * @param $code
   */
  public function watch(string $group, string $hook, string $action, $code)
  {
    $this->watches[$this->site][$group][$hook][$action][] = $code;
  }

  /**
   * @param string $group
   * @param string $hook
   * @param string $action
   */
  protected function register(string $group, string $hook, string $action)
  {
    $group = basename($group);

    $this->hooks[$this->site][$group][$hook][$action] = [];

    $directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/' . $this->site . DIRECTORY_SEPARATOR . $group;

    if (is_dir($directory)) {
      if ($dir = new DirectoryIterator($directory)) {
        foreach ($dir as $file) {
          if (!$file->isDot() && !$file->isDir() && ($file->getExtension() == 'php') && ($file->getBasename('.php') == $hook)) {
            $class = 'ClicShopping\OM\Module\Hooks\\' . $this->site . '\\' . $group . '\\' . $hook;

            if (method_exists($class, $action)) {
              $this->hooks[$this->site][$group][$hook][$action][] = $class;
            }
          }
        }
      }
    }

    $filter = [
      'site' => $this->site,
      'group' => $group,
      'hook' => $hook
    ];

    foreach (Apps::getModules('Hooks', null, $filter) as $k => $class) {
      if (method_exists($class, $action)) {
        $this->hooks[$this->site][$group][$hook][$action][] = $k;
      }
    }
  }
}
