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

  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class securityCheck_config_file_catalog
  {
    public string $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('Shop', 'modules/security_check/config_file_catalog', null, null, 'Shop');

    }

    public function pass()
    {
      return !FileSystem::isWritable(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/configure.php');
    }

    public function getMessage()
    {
      return CLICSHOPPING::getDef('warning_config_file_writeable', [
        'configure_file_path' => CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/configure.php'
      ]);
    }
  }