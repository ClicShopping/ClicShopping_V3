<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config\RV;

class RV extends \ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'reviews';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  /**
   * Initializes the module by setting its title, short title, introduction, and installation status.
   *
   * @return void
   */
  protected function init()
  {
    $this->title = $this->app->getDef('module_rv_title');
    $this->short_title = $this->app->getDef('module_rv_short_title');
    $this->introduction = $this->app->getDef('module_rv_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_REVIEWS_RV_STATUS') && (trim(CLICSHOPPING_APP_REVIEWS_RV_STATUS) != '');
  }

  /**
   * Installs the module by adding its reference to the installed modules configuration.
   *
   * @return void
   */
  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_REVIEWS_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_REVIEWS_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_REVIEWS_INSTALLED', implode(';', $installed));
  }

  /**
   * Uninstalls the module by removing its reference from the installed modules configuration.
   *
   * @return void
   */
  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_REVIEWS_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_REVIEWS_INSTALLED', implode(';', $installed));
    }
  }
}