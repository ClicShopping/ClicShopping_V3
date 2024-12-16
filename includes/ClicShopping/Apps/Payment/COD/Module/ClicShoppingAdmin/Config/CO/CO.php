<?php
/**
 * Class CO
 *
 * This class defines the configuration module for the "COD" (Cash on Delivery) payment app in the ClicShoppingAdmin.
 * It manages the installation and uninstallation of the COD payment module and initializes its properties.
 *
 * @package ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\CO
 */

namespace ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\CO;


/**
 * Class CO
 *
 * Represents the configuration module for managing the "COD" (Cash on Delivery) payment method
 * within the ClicShoppingAdmin application. Handles initialization, installation, and uninstalling
 * of the COD payment system, including updating the list of installed payment modules.
 *
 * @package ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\CO
 */
class CO extends \ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'COD';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  protected function init()
  {
    $this->title = $this->app->getDef('module_co_title');
    $this->short_title = $this->app->getDef('module_co_short_title');
    $this->introduction = $this->app->getDef('module_co_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_COD_CO_STATUS') && (trim(CLICSHOPPING_APP_COD_CO_STATUS) != '');
  }

  public function install()
  {
    parent::install();

    if (\defined('MODULE_PAYMENT_INSTALLED')) {
      $installed = explode(';', MODULE_PAYMENT_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_PAYMENT_INSTALLED', implode(';', $installed));
  }

  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_PAYMENT_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_PAYMENT_INSTALLED', implode(';', $installed));
    }
  }
}