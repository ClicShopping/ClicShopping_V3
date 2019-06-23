<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM;

  class Session
  {

    /**
     * Loads the session storage handler
     *
     * @param string $name The name of the session
     * @access public
     */

    protected static $driver;
    private static $default_driver = 'File';

    public static function load(string $name = null)
    {

      if (!isset(static::$driver)) {
        static::$driver = ClicShopping::configExists('store_sessions') ? ClicShopping::getConfig('store_sessions') : static::$default_driver;
      }

      if (!class_exists(__NAMESPACE__ . '\\Session\\' . static::$driver)) {
        trigger_error('ClicShopping\OM\Session::load(): Driver "' . static::$driver . '" does not exist, using default "' . static::$default_driver . '"', E_USER_ERROR);

        static::$driver = static::$default_driver;
      }

      $class_name = __NAMESPACE__ . '\\Session\\' . static::$driver;
      $obj = new $class_name();

      if (!isset($name)) {
        $name = 'clicshopid';
      }

      $obj->setName($name);

      $obj->setLifeTime(ini_get('session.gc_maxlifetime'));

      return $obj;
    }
  }
