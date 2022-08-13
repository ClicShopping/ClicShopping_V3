<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;

  class securityCheck_session_auto_start
  {
    public string $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/SecurityCheck/session_auto_start', null, null, 'Shop');
    }

    public function pass()
    {
      return ((bool)ini_get('session.auto_start') === false);
    }

    public function getMessage()
    {
      return CLICSHOPPING::getDef('warning_session_auto_start');
    }
  }