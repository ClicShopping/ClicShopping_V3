<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\GE;
/**
 * This class extends ConfigAbstract and defines configuration settings
 * for the "GE" module within the ClicShopping Admin Anti-spam application.
 *
 * The class contains properties to indicate installation status,
 * sorting order, and methods to initialize configuration module details and
 * handle installation and uninstallation processes.
 */
class GE extends \ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\ConfigAbstract
{
  public bool $is_installed = true;
//    public bool $is_uninstallable = true;
  public int|null $sort_order = 100000;

  /**
   * Initializes the module by setting its title and short title based on definitions retrieved from the application.
   *
   * @return void
   */
  protected function init()
  {
    $this->title = $this->app->getDef('module_ge_title');
    $this->short_title = $this->app->getDef('module_ge_short_title');
  }

  /**
   * Handles the installation process.
   *
   * @return bool Returns false to indicate the installation was unsuccessful.
   */
  public function install()
  {
    return false;
  }

  /**
   * Uninstalls the current module, plugin, or application.
   *
   * @return bool Returns false to indicate that the uninstallation is not implemented or unsuccessful.
   */
  public function uninstall()
  {
    return false;
  }
}