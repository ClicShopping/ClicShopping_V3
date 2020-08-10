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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class securityCheckExtended_admin_http_authentication
  {
    public $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/admin_http_authentication', null, null, 'Shop');

      $this->title = CLICSHOPPING::getDef('module_security_check_extended_admin_http_authentication_title');
    }

    public function pass()
    {

      return isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']);
    }

    public function getMessage()
    {
      return CLICSHOPPING::getDef('module_security_check_extended_admin_http_authentication_error');
    }
  }