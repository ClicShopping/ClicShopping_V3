<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\DefineLanguage\Module\ClicShoppingAdmin\Config\DL;

class DL extends \ClicShopping\Apps\Tools\DefineLanguage\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'define_language';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  /**
   * Initializes the module by setting its title, short title, introduction, and installation status
   * based on application definitions and configuration.
   *
   * @return void
   */
  protected function init()
  {
    $this->title = $this->app->getDef('module_cr_title');
    $this->short_title = $this->app->getDef('module_cr_short_title');
    $this->introduction = $this->app->getDef('module_cr_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_DEFINE_LANGUAGE_DL_STATUS') && (trim(CLICSHOPPING_APP_DEFINE_LANGUAGE_DL_STATUS) != '');
  }

  /**
   * Installs the module by adding its entry to the installed modules configuration.
   *
   * @return void
   */
  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_DEFINE_LANGUAGE_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_DEFINE_LANGUAGE_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_DEFINE_LANGUAGE_INSTALLED', implode(';', $installed));
  }

  /**
   * Uninstalls the module by removing its entry from the installed modules configuration.
   *
   * @return void
   */
  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_DEFINE_LANGUAGE_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_DEFINE_LANGUAGE_INSTALLED', implode(';', $installed));
    }
  }
}