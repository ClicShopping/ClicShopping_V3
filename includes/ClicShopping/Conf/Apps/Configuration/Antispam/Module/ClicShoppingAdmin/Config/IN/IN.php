<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\IN;

class IN extends \ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\ConfigAbstract
{
  protected $pm_code = 'antispam';
  public bool $is_uninstallable = true;
  public ?int $sort_order = 600;

  protected function init()
  {
    $this->title = $this->app->getDef('module_in_title');
    $this->short_title = $this->app->getDef('module_in_short_title');
    $this->introduction = $this->app->getDef('module_in_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_ANTISPAM_IN_STATUS') && (trim(CLICSHOPPING_APP_ANTISPAM_IN_STATUS) != '');
  }

  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_ANTISPAM_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_ANTISPAM_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_ANTISPAM_INSTALLED', implode(';', $installed));
  }

  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_ANTISPAM_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed, true);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_ANTISPAM_INSTALLED', implode(';', $installed));
    }
  }
}