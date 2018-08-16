<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  /**
   * Created by PhpStorm.
   * User: oitsuki
   * Date: 16-10-31
   * Time: 19:53
   */


  namespace ClicShopping\OM;
  class Is  {
    public static function __callStatic($name, $arguments) {
      if (class_exists(__NAMESPACE__ . '\\Is\\' . $name)) {
        return (bool)call_user_func_array([
          __NAMESPACE__ . '\\Is\\' . $name,
          'execute'
        ], $arguments);
      }
      return false;
    }
  }