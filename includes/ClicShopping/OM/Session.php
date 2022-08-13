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

  class Session
  {
    /**
     * Loads the session storage handler
     *
     * @param string $name The name of the session
     *
     */

    protected static string $driver;
    protected static string $default_driver = 'File';

    /**
     * @param string|null $name
     * @return mixed
     */
    public static function load(?string $name = null)
    {

      if (!isset(static::$driver)) {
        static::$driver = CLICSHOPPING::configExists('store_sessions') ? CLICSHOPPING::getConfig('store_sessions') : static::$default_driver;
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
