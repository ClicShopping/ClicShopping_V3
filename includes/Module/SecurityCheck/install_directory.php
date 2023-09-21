<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class securityCheck_install_directory
{
  public string $type = 'warning';

  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/install_directory', null, null, 'Shop');
  }

  public function pass()
  {
    return !is_dir(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'install');
  }

  public function getMessage()
  {
    return CLICSHOPPING::getDef('warning_install_directory_exists', [
      'install_path' => CLICSHOPPING::getConfig('dir_root', 'Shop') . 'install'
    ]);
  }
}
