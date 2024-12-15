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

/**
 * The Hooks class abstracts the functionality for managing, registering, and executing hooks within
 * the ClicShopping framework. Hooks are used to provide a flexible and extensible way to execute
 * custom code at various points in the application lifecycle.
 */
class Hooks
{
  protected ?string $site;
  protected array $hooks = [];
  protected array $watches = [];

  /**
   * Constructor method for initializing the object with a site value.
   *
   * @param string|null $site Optional. The site name. If not provided, it defaults to the value returned by CLICSHOPPING::getSite().
   * @return void
   */
  public function __construct(?string $site = null)
  {
    if (!isset($site)) {
      $site = CLICSHOPPING::getSite();
    }

    $this->site = basename($site);
  }

  /**
   * Executes a specified hook and action for a given group and collects the results.
   *
   * @param string $group The group name of the hook.
   * @param string $hook The specific hook to be called.
   * @param array|null $parameters Optional parameters to be passed to the hook/action.
   * @param string|null $action The action to be executed, defaults to 'execute' if not provided.
   * @return array The results returned by the executed hook actions.
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
   * Combines and returns the result of calling the 'call' method with the provided arguments.
   *
   * @return string Concatenated string resulting from the invocation of the 'call' method with passed arguments.
   */
  public function output(): string
  {
    return implode('', call_user_func_array([$this, 'call'], \func_get_args()));
  }

  /**
   * Add a callback or action to a specific hook within a group for the current site.
   *
   * @param string $group The group to which the hook belongs.
   * @param string $hook The hook within the group to watch.
   * @param string $action The specific action within the hook to associate the code with.
   * @param mixed $code The code or callback to be executed when the action is triggered.
   * @return void
   */
  public function watch(string $group, string $hook, string $action, $code): void
  {
    $this->watches[$this->site][$group][$hook][$action][] = $code;
  }

  /**
   * Registers a specific action to a hook within a group for a given site context.
   *
   * @param string $group The name of the group to register the hook under.
   * @param string $hook The name of the specific hook to register.
   * @param string $action The action to be executed when the hook is called.
   * @return void
   */
  protected function register(string $group, string $hook, string $action): void
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
