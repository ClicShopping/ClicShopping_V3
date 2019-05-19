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

  use ClicShopping\OM\CLICSHOPPING;

  class Session
  {

    /**
     * Loads the session storage handler
     *
     * @param string $name The name of the session
     * @access public
     */
    public static function load($name = null)
    {

      $class_name = 'ClicShopping\\OM\\Session\\' . CLICSHOPPING::getConfig('store_sessions');

      if (!class_exists($class_name)) {
        trigger_error('Session Handler \'' . $class_name . '\' does not exist, using default \'ClicShopping\\OM\\Session\\File\'', E_USER_NOTICE);

        $class_name = 'ClicShopping\\OM\\Session\\File';
      } elseif (!is_subclass_of($class_name, 'ClicShopping\OM\SessionAbstract')) {
        trigger_error('Session Handler \'' . $class_name . '\' does not extend ClicShopping\\OM\\SessionAbstract, using default \'ClicShopping\\OM\\Session\\File\'', E_USER_NOTICE);

        $class_name = 'ClicShopping\\OM\\Session\\File';
      }

      $obj = new $class_name();

      if (!isset($name)) {
        $name = 'clicshopid';
      }

      $obj->setName($name);

      return $obj;
    }
  }
