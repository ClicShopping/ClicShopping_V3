<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Module\ClicShoppingAdmin\Config\PM;

/**
 * Class PM
 *
 * This class defines the Page Manager module configuration for the ClicShoppingAdmin.
 * It extends the abstract configuration class ConfigAbstract and provides
 * initialization, installation, and uninstallation logic for the module.
 *
 * Properties:
 * - $pm_code: The code identifier for the module.
 * - $is_uninstallable: A flag indicating if the module can be uninstalled.
 * - $sort_order: The sort order of the module in the listing.
 *
 * Methods:
 * - init(): Initializes the module by defining its title, short title, introduction, and installation status.
 * - install(): Installs the module by adding it to the configuration parameter
 *   'MODULE_MODULES_PAGE_MANAGER_INSTALLED'.
 * - uninstall(): Uninstalls the module by removing it from the configuration parameter
 *   'MODULE_MODULES_PAGE_MANAGER_INSTALLED'.
 */
class PM extends \ClicShopping\Apps\Communication\PageManager\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'PageManager';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  protected function init()
  {
    $this->title = $this->app->getDef('module_pm_title');
    $this->short_title = $this->app->getDef('module_pm_short_title');
    $this->introduction = $this->app->getDef('module_pm_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS') && (trim(CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS) != '');
  }

  /**
   * Installs the current module by adding it to the list of installed modules.
   *
   * @return void
   */
  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_PAGE_MANAGER_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_PAGE_MANAGER_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_PAGE_MANAGER_INSTALLED', implode(';', $installed));
  }

  /**
   * Uninstalls the module by removing it from the list of installed modules
   * and updating the configuration parameter.
   *
   * @return void
   */
  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_PAGE_MANAGER_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_PAGE_MANAGER_INSTALLED', implode(';', $installed));
    }
  }
}