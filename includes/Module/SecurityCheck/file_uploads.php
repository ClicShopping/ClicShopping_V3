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
  use ClicShopping\OM\CLICSHOPPING;

  class securityCheck_file_uploads
  {
    public string $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/SecurityCheck/file_uploads', null, null, 'Shop');

    }

    public function pass()
    {
      return (bool)ini_get('file_uploads');
    }

    public function getMessage()
    {
      return CLICSHOPPING::getDef('warning_file_uploads_disabled');
    }
  }

