<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxGeoZones\Module\ClicShoppingAdmin\Config\TG;

class TG extends \ClicShopping\Apps\Configuration\TaxGeoZones\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'tax_geo_zones';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  /**
   * Initializes the module by setting its title, short title, introduction,
   * and installation status based on the application's definitions and configuration.
   *
   * @return void
   */
  protected function init()
  {
    $this->title = $this->app->getDef('module_tg_title');
    $this->short_title = $this->app->getDef('module_tg_short_title');
    $this->introduction = $this->app->getDef('module_tg_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_TAX_GEO_ZONES_TG_STATUS') && (trim(CLICSHOPPING_APP_TAX_GEO_ZONES_TG_STATUS) != '');
  }

  /**
   * Installs the module and updates the configuration parameter to include the module identifier.
   *
   * @return void
   */
  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_TAX_GEO_ZONES_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_TAX_GEO_ZONES_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_TAX_GEO_ZONES_INSTALLED', implode(';', $installed));
  }

  /**
   * Uninstalls the module by removing its configuration entry from the list of installed modules
   * and updating the stored configuration parameters accordingly.
   *
   * @return void
   */
  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_TAX_GEO_ZONES_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_TAX_GEO_ZONES_INSTALLED', implode(';', $installed));
    }
  }
}