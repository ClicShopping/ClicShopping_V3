<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class securityCheck_download_directory
  {
    public string $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/SecurityCheck/download_directory', null, null, 'Shop');
    }

    public function pass()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (DOWNLOAD_ENABLED != 'true') {
        return true;
      }

      return is_dir($CLICSHOPPING_Template->getPathDownloadShopDirectory('Private'));
    }

    public function getMessage()
    {
      return CLICSHOPPING::getDef('warning_download_directory_non_existent', [
        'download_path' => CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/Download/Private/'
      ]);
    }
  }