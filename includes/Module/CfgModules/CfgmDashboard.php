<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;

class CfgmDashboard
{
  public string $code = 'dashboard';
  public string $directory;
  public $language_directory;
  public $site = 'ClicShoppingAdmin';
  public string $key = 'MODULE_ADMIN_DASHBOARD_INSTALLED';
  public $title;
  public bool $template_integration = false;

  /**
   * Initializes the dashboard module by setting up necessary directories and the module's title.
   *
   * @return void
   */
  public function __construct()
  {
    $this->directory = CLICSHOPPING::getConfig('dir_root', $this->site) . 'includes/modules/dashboard/';
    $this->language_directory = CLICSHOPPING::getConfig('dir_root') . 'includes/languages/';

    $this->title = CLICSHOPPING::getDef('module_cfg_module_dashboard_title');
  }
}