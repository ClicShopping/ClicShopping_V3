<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TemplateEmail\Module\ClicShoppingAdmin\Config\TE;
/**
 * This class represents the Template Email module configuration within the admin configuration panel
 * of the ClicShopping application. It extends the ConfigAbstract class and provides functionalities
 * to handle the installation and uninstallation of the Template Email module.
 */

class TE extends \ClicShopping\Apps\Configuration\TemplateEmail\Module\ClicShoppingAdmin\Config\ConfigAbstract
{

  protected $pm_code = 'template_email';

  public bool $is_uninstallable = true;
  public int|null $sort_order = 400;

  /**
   * Initializes the module by setting its title, short title, introduction,
   * and determining its installation status based on predefined constants.
   *
   * @return void
   */
  protected function init()
  {
    $this->title = $this->app->getDef('module_te_title');
    $this->short_title = $this->app->getDef('module_te_short_title');
    $this->introduction = $this->app->getDef('module_te_introduction');
    $this->is_installed = \defined('CLICSHOPPING_APP_TEMPLATE_EMAIL_TE_STATUS') && (trim(CLICSHOPPING_APP_TEMPLATE_EMAIL_TE_STATUS) != '');
  }

  /**
   * Installs the module and updates the configuration by adding the module's details
   * to the installed modules list.
   *
   * @return void
   */
  public function install()
  {
    parent::install();

    if (\defined('MODULE_MODULES_TEMPLATE_EMAIL_INSTALLED')) {
      $installed = explode(';', MODULE_MODULES_TEMPLATE_EMAIL_INSTALLED);
    }

    $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

    $this->app->saveCfgParam('MODULE_MODULES_TEMPLATE_EMAIL_INSTALLED', implode(';', $installed));
  }

  /**
   * Uninstalls the module and updates the configuration by removing the module's details
   * from the installed modules list.
   *
   * @return void
   */
  public function uninstall()
  {
    parent::uninstall();

    $installed = explode(';', MODULE_MODULES_TEMPLATE_EMAIL_INSTALLED);
    $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

    if ($installed_pos !== false) {
      unset($installed[$installed_pos]);

      $this->app->saveCfgParam('MODULE_MODULES_TEMPLATE_EMAIL_INSTALLED', implode(';', $installed));
    }
  }
}