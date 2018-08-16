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

  namespace ClicShopping\OM;

  class Registry {
    private static $data = [];

    public static function get($key)   {
      if (!static::exists($key)) {
        trigger_error('ClicShopping\OM\Registry::get - ' . $key . ' is not registered');

          return false;
      }

      return static::$data[$key];
    }

    public static function set($key, $value, $force = false)  {
      if (!is_object($value)) {
        trigger_error('ClicShopping\OM\Registry::set - ' . $key . ' is not an object and cannot be set in the registry');

        return false;
      }

        if (static::exists($key) && ($force !== true)) {
          trigger_error('ClicShopping\OM\Registry::set - ' . $key . ' already registered and is not forced to be replaced');

          return false;
        }

        static::$data[$key] = $value;
    }

    public static function exists($key) {
        return array_key_exists($key, static::$data);
    }
  }
