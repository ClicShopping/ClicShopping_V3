<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Langues\Module\ClicShoppingAdmin\Config\LG;

class LG extends \ClicShopping\Apps\Configuration\Langues\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'langues';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  protected function init()
  {
    $this->title = $this->app->getDef('module_lg_title');
    $this->short_title = $this->app->getDef('module_lg_short_title');
    $this->introduction = $this->app->getDef('module_lg_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_LANGUES_LG_STATUS') && (trim(CLICSHOPPING_APP_LANGUES_LG_STATUS) != '');
  }

  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_LANGUES_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_LANGUES_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_LANGUES_INSTALLED', implode(';', $installed));
  }

  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_LANGUES_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_LANGUES_INSTALLED', implode(';', $installed));
    }
  }
}