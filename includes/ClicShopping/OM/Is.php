<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

class Is
{
  public static function __callStatic(string $name, array $arguments): bool
  {
    $class = __NAMESPACE__ . '\\Is\\' . $name;

    try {
      if (!class_exists($class)) {
        throw new \Exception('ClicShopping\Is module class does not exist: ' . $class);
      }

      if (!is_subclass_of($class, 'ClicShopping\\OM\\IsInterface')) {
        throw new \Exception('ClicShopping\Is module class does not implement ClicShopping\OM\IsInterface: ' . $class);
      }

      $callable = [
        $class,
        'execute'
      ];

      if (is_callable($callable)) {
        return \call_user_func_array($callable, $arguments);
      }
    } catch (\Exception $e) {
      trigger_error($e->getMessage());
    }

    return false;
  }
}