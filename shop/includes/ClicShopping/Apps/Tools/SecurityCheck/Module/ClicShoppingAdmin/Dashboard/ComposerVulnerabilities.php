<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\SecurityCheck\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\SecurityCheck\SecurityCheck as SecurityCheckApp;

  use SensioLabs\Security\SecurityChecker;

  class ComposerVulnerabilities extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {

    protected $lang;
    protected $app;

    protected function init()
    {

      if (!Registry::exists('SecurityCheck')) {
        Registry::set('SecurityCheck', new SecurityCheckApp());
      }

      $this->app = Registry::get('SecurityCheck');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/composer_vulnerabilities');

      $this->title = $this->app->getDef('module_admin_dashboard_composer_vulnerabilities_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_composer_vulnerabilities_app_description');

      if (defined('MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_STATUS')) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_STATUS == 'True');
      }
    }

    public static function checkVulnerabilities(): array
    {
      $checker = new SecurityChecker();
      $result = $checker->check(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'composer.lock', 'json');
      $alerts = json_decode((string)$result, true);

      return $alerts;
    }

    public function getOutput()
    {
      $result = static::checkVulnerabilities();
      $count = count($result);

      $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_CONTENT_WIDTH;
      $output = '';

      if ($count == 0) {
        $output = '<span class="' . $content_width . '">';
        $output .= '<div class="separator"></div>';
        $output .= '<div class="alert alert-danger">';
        $output .=  '<div>' . $this->app->getDef('module_admin_dashboard_composer_vulnerabilities_app_title_info') . '</div>';
        $output .=  '<div class="separator"></div>';

        foreach ($result as $item) {
          $output .=  '<div class="col-md-12">' . $item . '</div>';
        }

        $output .= '</div>';
        $output .= '</span>';
      }

      return $output;
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to enable this Module ?',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_STATUS',
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
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_SORT_ORDER',
          'configuration_value' => '465',
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
      return ['MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_STATUS',
        'MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_CONTENT_WIDTH',
        'MODULE_ADMIN_DASHBOARD_COMPOSER_VULNERABILITIES_APP_SORT_ORDER'
      ];
    }
  }
