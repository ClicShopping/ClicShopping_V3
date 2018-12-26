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

  use ClicShopping\OM\Registry;

  class securityCheck_file_uploads {
    public $type = 'warning';

    public function __construct() {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/security_check/file_uploads',null, null, 'Shop');

    }

    public function pass() {
      return (bool)ini_get('file_uploads');
    }

    public function getMessage() {
      return CLICSHOPPING::getDef('warning_file_uploads_disabled');
    }
  }

