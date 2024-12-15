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
use ClicShopping\OM\FileSystem;
use ClicShopping\OM\Registry;

class securityCheck_config_file_catalog
{
  public string $type = 'warning';

  /**
   * Constructor method.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('Shop', 'modules/SecurityCheck/config_file_catalog', null, null, 'Shop');

  }

  /**
   * Checks if the configuration file is not writable.
   *
   * @return bool Returns true if the configuration file is not writable, otherwise false.
   */
  public function pass()
  {
    return !FileSystem::isWritable(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/configure.php');
  }

  /**
   * Retrieves the warning message related to the writable configuration file.
   *
   * @return string The warning message with the configured file path.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('warning_config_file_writeable', [
      'configure_file_path' => CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/configure.php'
    ]);
  }
}