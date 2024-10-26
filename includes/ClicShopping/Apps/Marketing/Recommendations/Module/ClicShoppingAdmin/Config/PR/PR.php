<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Module\ClicShoppingAdmin\Config\PR;

class PR extends \ClicShopping\Apps\Marketing\Recommendations\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'product_recommandations';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  protected function init()
  {
    $this->title = $this->app->getDef('module_pr_title');
    $this->short_title = $this->app->getDef('module_pr_short_title');
    $this->introduction = $this->app->getDef('module_pr_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS') && (trim(CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS) != '');
  }

  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_PRODUCTS_PRODUCT_RECOMMENDATIONS_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_PRODUCTS_PRODUCT_RECOMMENDATIONS_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_PRODUCTS_PRODUCT_RECOMMENDATIONS_INSTALLED', implode(';', $installed));
  }

  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_PRODUCTS_PRODUCT_RECOMMENDATIONS_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_PRODUCTS_PRODUCT_RECOMMENDATIONS_INSTALLED', implode(';', $installed));
    }
  }
}