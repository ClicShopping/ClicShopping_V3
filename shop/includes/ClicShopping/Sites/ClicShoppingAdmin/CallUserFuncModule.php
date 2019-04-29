<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;

  class CallUserFuncModule {

    public static function execute($function, $default = null, $key = null) {
      if ( strpos($function, '::') !== false ) {
        $class_method = explode('::', $function);

        return call_user_func(array($class_method[0], $class_method[1]), $default, $key);
      } else {

        $function_name = $function;
        $function_parameter = '';

        if ( strpos($function, '(') !== false ) {
          $function_array = explode('(', $function, 2);

          $function_name = $function_array[0];
          $function_parameter = substr($function_array[1], 0, -1);
        }

        if ( !function_exists($function_name) ) {
          if (is_file(CLICSHOPPING::BASE_DIR . 'Sites/ClicShoppingAdmin/Assets/CfgParameters/' . $function_name . '.php')) {
            include_once(CLICSHOPPING::BASE_DIR . 'Sites/ClicShoppingAdmin/Assets/CfgParameters/' . $function_name . '.php');
          } else {
            include_once(CLICSHOPPING::BASE_DIR . 'Custom/Sites/ClicShoppingAdmin/Assets/CfgParameters/' . $function_name . '.php');
          }
        }

        if ( !empty($function_parameter) ) {
          return call_user_func($function_name, $function_parameter, $default, $key);
        } else {
          return call_user_func($function_name, $default, $key);
        }
      }
    }
  }

