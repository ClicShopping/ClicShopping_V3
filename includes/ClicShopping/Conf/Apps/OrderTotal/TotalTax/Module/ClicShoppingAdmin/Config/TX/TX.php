<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\TotalTax\Module\ClicShoppingAdmin\Config\TX;

class TX extends \ClicShopping\Apps\OrderTotal\TotalTax\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'TotalTax';

  public bool $is_uninstallable = true;
  public ?int $sort_order = 400;

  protected function init()
  {
    $this->title = $this->app->getDef('module_tx_title');
    $this->short_title = $this->app->getDef('module_tx_short_title');
    $this->introduction = $this->app->getDef('module_tx_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_STATUS') && (trim(CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_STATUS) != '');
  }

  public function install()
  {
    parent::install();

    if (\defined('MODULE_ORDER_TOTAL_INSTALLED')) {
      $installed = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_ORDER_TOTAL_INSTALLED', implode(';', $installed));
  }

  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_ORDER_TOTAL_INSTALLED', implode(';', $installed));
    }
  }
}