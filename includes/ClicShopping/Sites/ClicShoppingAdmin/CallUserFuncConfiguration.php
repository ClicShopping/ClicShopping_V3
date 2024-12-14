<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use function call_user_func;
/**
 * This class provides a static method to dynamically execute a function or a class method.
 * It supports execution of global functions as well as class methods specified in the format "Class::Method".
 * If the specified function or file does not exist, it attempts to include the required file from predefined directories.
 */
class CallUserFuncConfiguration
{
  public static function execute($function, $default = null, $key = null)
  {
    if (str_contains($function, '::')) {
      $class_method = explode('::', $function);

      return call_user_func(array($class_method[0], $class_method[1]), $default, $key);
    } else {
      $function_name = $function;
      $function_parameter = '';

      if (str_contains($function, '(')) {
        $function_array = explode('(', $function, 2);

        $function_name = $function_array[0];
        $function_parameter = substr($function_array[1], 0, -1);
      }

      if (!function_exists($function_name)) {
        if (is_file(CLICSHOPPING::BASE_DIR . 'Sites/ClicShoppingAdmin/Assets/CfgParameters/' . $function_name . '.php')) {
          include(CLICSHOPPING::BASE_DIR . 'Sites/ClicShoppingAdmin/Assets/CfgParameters/' . $function_name . '.php');
        } else {
          include(CLICSHOPPING::BASE_DIR . 'Custom/SitesClicShoppingAdmin/Assets/CfgParameters/' . $function_name . '.php');
        }
      }

      if (!empty($function_parameter)) {
        return call_user_func($function_name, $function_parameter, $default, $key);
      } else {
        return call_user_func($function_name, $default, $key);
      }
    }
  }
}