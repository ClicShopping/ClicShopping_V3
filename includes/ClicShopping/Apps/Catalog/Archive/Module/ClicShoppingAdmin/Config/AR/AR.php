<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Archive\Module\ClicShoppingAdmin\Config\AR;

class AR extends \ClicShopping\Apps\Catalog\Archive\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'archive';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  /**
   * Initializes the module by setting its title, short title, introduction, and installation status.
   *
   * @return void
   */
  protected function init()
  {
    $this->title = $this->app->getDef('module_ar_title');
    $this->short_title = $this->app->getDef('module_ar_short_title');
    $this->introduction = $this->app->getDef('module_ar_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_ARCHIVE_AR_STATUS') && (trim(CLICSHOPPING_APP_ARCHIVE_AR_STATUS) != '');
  }

  /**
   * Installs the module by adding its identifier to the list of installed modules in the configuration.
   *
   * @return void
   */
  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_PRODUCTS_ARCHIVE_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_PRODUCTS_ARCHIVE_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_PRODUCTS_ARCHIVE_INSTALLED', implode(';', $installed));
  }

  /**
   * Uninstalls the current module by removing its entry from the list of installed modules.
   *
   * @return void
   */
  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_PRODUCTS_ARCHIVE_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_PRODUCTS_ARCHIVE_INSTALLED', implode(';', $installed));
    }
  }
}