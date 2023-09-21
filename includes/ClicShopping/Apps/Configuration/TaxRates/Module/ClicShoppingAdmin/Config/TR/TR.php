<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxRates\Module\ClicShoppingAdmin\Config\TR;

class TR extends \ClicShopping\Apps\Configuration\TaxRates\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'tax_rates';

  public bool $is_uninstallable = true;
  public ?int $sort_order = 400;

  protected function init()
  {
    $this->title = $this->app->getDef('module_tr_title');
    $this->short_title = $this->app->getDef('module_tr_short_title');
    $this->introduction = $this->app->getDef('module_tr_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_TAX_RATES_TR_STATUS') && (trim(CLICSHOPPING_APP_TAX_RATES_TR_STATUS) != '');
  }

  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_TAX_RATES_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_TAX_RATES_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_TAX_RATES_INSTALLED', implode(';', $installed));
  }

  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_TAX_RATES_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_TAX_RATES_INSTALLED', implode(';', $installed));
    }
  }
}