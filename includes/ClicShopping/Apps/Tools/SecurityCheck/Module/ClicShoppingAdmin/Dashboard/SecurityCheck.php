<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecurityCheck\Module\ClicShoppingAdmin\Dashboard;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\SecurityCheck\SecurityCheck as SecurityCheckApp;

class SecurityCheck extends \ClicShopping\OM\Modules\AdminDashboardAbstract
{
  protected mixed $lang;
  protected mixed $app;
  public $group;

  protected function init()
  {
    if (!Registry::exists('SecurityCheck')) {
      Registry::set('SecurityCheck', new SecurityCheckApp());
    }

    $this->app = Registry::get('SecurityCheck');
    $this->lang = Registry::get('Language');

    $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/security_check');

    $this->title = $this->app->getDef('module_admin_dashboard_security_checks_app_title');
    $this->description = $this->app->getDef('module_admin_dashboard_security_checks_app_description');

    if (\defined('MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_STATUS')) {
      $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_SORT_ORDER;
      $this->enabled = (MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_STATUS == 'True');
    }
  }

  public function getOutput()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    $secCheck_types = ['info', 'warning', 'error'];

    $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));

    $secmodules_array = [];

    if ($secdir = @dir(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/SecurityCheck/')) {
      while (false !== ($file = $secdir->read())) {
        if (!is_file(CLICSHOPPING::getConfig('dir_root') . 'includes/Module/SecurityCheck/' . $file)) {
          if (substr($file, strrpos($file, '.')) == $file_extension) {
            $secmodules_array[] = $file;
          }
        }
      }
      sort($secmodules_array);
      $secdir->close();
    }

    foreach ($secmodules_array as $secmodule) {
      include(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/SecurityCheck/' . $secmodule);

      $secclass = 'securityCheck_' . substr($secmodule, 0, strrpos($secmodule, '.'));
      if (class_exists($secclass)) {
        $secCheck = new $secclass;

        if (!$secCheck->pass()) {
          if (!\in_array($secCheck->type, $secCheck_types, true)) {
            $secCheck->type = 'info';
          }

          $CLICSHOPPING_MessageStack->add($secCheck->getMessage(), $secCheck->type);
        }
      }
    }

    if (!$CLICSHOPPING_MessageStack->exists('securityCheckModule')) {
      $CLICSHOPPING_MessageStack->add($this->app->getDef('module_admin_dashboard_security_checks_app_success'), 'success');
    }

    $output = '<div class="clearfix"></div>';
    $output .= '<div>' . $CLICSHOPPING_MessageStack->get('main') . '</div>';


    return $output;
  }

  public function Install()
  {
    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to enable this Module ?',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to enable this Module ?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
        'date_added' => 'now()'
      ]
    );

    $this->app->db->save('configuration', [
        'configuration_title' => 'Select the width to display',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_CONTENT_WIDTH',
        'configuration_value' => '12',
        'configuration_description' => 'Select a number between 1 to 12',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_content_module_width_pull_down',
        'date_added' => 'now()'
      ]
    );

    $this->app->db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_SORT_ORDER',
        'configuration_value' => '455',
        'configuration_description' => 'Sort order of display. Lowest is displayed first.',
        'configuration_group_id' => '6',
        'sort_order' => '99',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  public function keys()
  {
    return ['MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_STATUS',
      'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_CONTENT_WIDTH',
      'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_APP_SORT_ORDER'
    ];
  }
}
