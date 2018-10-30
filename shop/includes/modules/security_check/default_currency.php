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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class securityCheck_default_currency {
    public $type = 'error';

    public function __construct() {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/security_check/default_currency',null, null, 'Shop');
    }

    public function pass() {
      return defined('DEFAULT_CURRENCY');
    }

    public function getMessage() {
      return CLICSHOPPING::getDef('error_no_default_currency_defined');
    }
  }