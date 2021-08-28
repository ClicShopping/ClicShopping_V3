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

  namespace ClicShopping\Apps\Tools\Upgrade\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;

  use ClicShopping\Apps\Tools\Upgrade\Upgrade as UpgradeApp;

  class LatestApps extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {
    protected mixed $lang;
    protected mixed $app;
    public $group;

    protected function init()
    {
      if (!Registry::exists('Upgrade')) {
        Registry::set('Upgrade', new UpgradeApp());
      }

      $this->app = Registry::get('Upgrade');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/latest_apps');

      $this->title = $this->app->getDef('module_admin_dashboard_clicshopping_latest_apps_title');
      $this->description = $this->app->getDef('module_admin_dashboard_clicshopping_latest_apps_description');

      if (\defined('MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_STATUS')) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_STATUS == 'True');
      }
    }

    public function getOutput()
    {
      $check_source = file_get_contents('https://www.clicshopping.org/forum/files/files.xml');

      if ($check_source !== false  && !empty($check_source)) {
        $feed = simplexml_load_string($check_source);

        $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_CONTENT_WIDTH;

        $output = '<div class="' . $content_width . '">';
        $output .= '<div class="separator"></div>';
        $output .= '<table
                      id="table"
                      data-toggle="table"
    data-icons-prefix="bi"
    data-icons="icons"
                      data-toolbar="#toolbar"
                      data-buttons-class="primary"
                      data-show-toggle="true"
                      data-show-columns="true"
                      data-mobile-responsive="true">';
        $output .= '<thead class="dataTableHeadingRow">';
        $output .= '<tr>';
        $output .= '<th data-field="logo">' . HTML::image(CLICSHOPPING::link('Shop/images/logo_clicshopping_24.webp'), 'ClicShopping') . '</th>';
        $output .= '<th data-field="title" data-switchable="false">' . $this->app->getDef('text_module_admin_dashboard_clicshopping_latest_apps_tilte') . '</th>';
        $output .= '<th data-field="date" class="text-end">' . $this->app->getDef('text_module_admin_dashboard_clicshopping_latest_apps_date') . '</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        $i = 1;
        $display_max = (int)MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_DISPLAY;

        foreach ($feed->channel as $item) {
          foreach ($item as $value) {
            if (!empty($value->title)) {
              if ($i <= $display_max) {
                $output .= '<tr class="backgroundBlank">';
                $output .= '<td>' . $i . '</td>';
                $output .= '<td>' . HTML::link($value->link, $value->title, 'target="_blank" rel="noreferrer"') . '</td>';
                $output .= '<td>' . DateTime::toShort($value->pubDate) . '</td>';
                $output .= '</tr>';
              }
              $i++;
            }
          }
        }

        $output .= '<tr>';
        $output .= '<td>' . HTML::button($this->app->getDef('text_module_admin_dashboard_clicshopping_latest_apps_search'), null, CLICSHOPPING::link(null, 'A&Tools\Upgrade&Upgrade'), 'primary', null, 'sm') . '</td>';
        $output .= '<td ></td>';
        $output .= '<td>' . HTML::button($this->app->getDef('text_module_admin_dashboard_clicshopping_latest_apps_join_community'), null, 'https://www.clicshopping.org', 'info', ['params' => 'target="_blank" rel="noreferrer"'], 'sm') . '</td>';
        $output .= '</tr>';
        $output .= '</tbody>';
        $output .= '</table>';
        $output .= '</div>';

        return $output;
      }
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to display the latest Apps / modules update ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $this->app->db->save('configuration', [
          'configuration_title' => 'How many Apps / module do you want to diplay',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_DISPLAY',
          'configuration_value' => '5',
          'configuration_description' => 'Select a number between 1 to 10',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $this->app->db->save('configuration', [
          'configuration_title' => 'Select the width to display',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 to 12',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $this->app->db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_SORT_ORDER',
          'configuration_value' => '150',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '60',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys()
    {
      return [
        'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_STATUS',
        'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_DISPLAY',
        'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_CONTENT_WIDTH',
        'MODULE_ADMIN_DASHBOARD_CLICSHOPPING_LASTEST_APPS_SORT_ORDER'
      ];
    }
  }
