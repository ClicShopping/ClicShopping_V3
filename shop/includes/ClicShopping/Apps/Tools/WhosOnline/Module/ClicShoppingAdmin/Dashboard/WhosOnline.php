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

  namespace ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\WhosOnline\WhosOnline as WhosOnlineApp;

  class WhosOnline extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {

    protected $lang;
    protected $app;

    protected function init()
    {

      if (!Registry::exists('WhosOnline')) {
        Registry::set('WhosOnline', new WhosOnlineApp());
      }

      $this->app = Registry::get('WhosOnline');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/whos_online');

      $this->title = $this->app->getDef('module_admin_dashboard_whos_online_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_whos_online_app_description');

      if (defined('MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_STATUS')) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_STATUS == 'True');
      }
    }

    public function getOutput()
    {
      $xx_mins_ago = (time() - 900);

      $Qdelete = $this->app->db->prepare('delete
                                          from :table_whos_online
                                          where time_last_click < :time_last_click
                                        ');
      $Qdelete->bindValue(':time_last_click', $xx_mins_ago);
      $Qdelete->execute();

      $QwhosOnline = $this->app->db->prepare('select customer_id,
                                                    full_name,
                                                    ip_address,
                                                    user_agent
                                              from  :table_whos_online
                                              limit 5
                                          ');
      $QwhosOnline->execute();

      if ($QwhosOnline->rowCount() > 0) {

        $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_CONTENT_WIDTH;

        $output = '<span class="' . $content_width . '">';
        $output .= '<div class="separator"></div>';

        $output .= '<table class="table table-hover" width="100%">';
        $output .= '<thead>';
        $output .= '<tr class="dataTableRow backgroundBlank">';
        $output .= '<th width="10%">' . $this->app->getDef('module_admin_dashboard_whos_online_app_table_heading_online') . '</th>';
        $output .= '<th width="20%" class="text-md-center">' . $this->app->getDef('module_admin_dashboard_whos_online_app_table_heading_full_name') . '</th>';
        $output .= '<th width="10%" class="text-md-center">' . $this->app->getDef('module_admin_dashboard_whos_online_app_table_heading_ip_address') . '</th>';
        $output .= '<th width="60%" class="text-md-center">' . $this->app->getDef('module_admin_dashboard_whos_online_app_table_heading_user_agent') . '</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        while ($QwhosOnline->fetch()) {

          $time_online = (time() - $QwhosOnline->value('time_entry'));

          $output .= '<tr class="dataTableRow">';
          $output .= '<td class="dataTableContent">' . gmdate('H:i:s', $time_online) . '</td> ';

          if ($QwhosOnline->valueInt('customer_id') == 0) {
            $output .= '<td class="dataTableContent text-md-left">' . $QwhosOnline->value('full_name') . '</td>';
          } else {
            $output .= '<td class="dataTableContent"><a href="' . CLICSHOPPING::link(null, 'A&Customers\Customers&Customers&Edit&cID=' . $QwhosOnline->valueInt('customer_id')) . '" title="View Customer">' . $QwhosOnline->value('full_name') . '</a></td>';
          }

          $output .= '<td class="dataTableContent text-md-center"><a href="https://ip-lookup.net/index.php?ip=' . urlencode($QwhosOnline->valueInt('ip_address')) . '" title="Lookup" target="_blank" rel="noreferrer">' . $QwhosOnline->value('ip_address') . '</a></td>';
          $output .= '<td class="dataTableContent">' . $QwhosOnline->value('user_agent') . '</td>';
          $output .= '</tr>';
        } // end while

        $output .= '</tbody>';
        $output .= '</table>';
        $output .= '</span>';
        $output .= '<div>';
        $output .= '<p class="text-md-right" style="size:0.2rem;"><small>' . $this->app->getDef('module_admin_dashboard_whos_online_app_customers_online') . ' ' . $QwhosOnline->rowCount() . '</small></p>';
        $output .= '</div>';
      }

      return $output;
    }

    public function Install()
    {
      if ($this->lang->getId() != 2) {

        $this->app->db->save('configuration', [
            'configuration_title' => 'Souhaitez vous activer ce module ?',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_STATUS',
            'configuration_value' => 'True',
            'configuration_description' => 'Souhaitez vous activer ce module ?',
            'configuration_group_id' => '6',
            'sort_order' => '1',
            'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
            'date_added' => 'now()'
          ]
        );

        $this->app->db->save('configuration', [
            'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_CONTENT_WIDTH',
            'configuration_value' => '12',
            'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
            'configuration_group_id' => '6',
            'sort_order' => '1',
            'set_function' => 'clic_cfg_set_content_module_width_pull_down',
            'date_added' => 'now()'
          ]
        );

        $this->app->db->save('configuration', [
            'configuration_title' => 'Ordre de tri d\'affichage',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_SORT_ORDER',
            'configuration_value' => '80',
            'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
            'configuration_group_id' => '6',
            'sort_order' => '2',
            'set_function' => '',
            'date_added' => 'now()'
          ]
        );

      } else {

        $this->app->db->save('configuration', [
            'configuration_title' => 'Do you want to enable this module ?',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_STATUS',
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
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_CONTENT_WIDTH',
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
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_SORT_ORDER',
            'configuration_value' => '90',
            'configuration_description' => 'Sort order of display. Lowest is displayed first.',
            'configuration_group_id' => '6',
            'sort_order' => '2',
            'set_function' => '',
            'date_added' => 'now()'
          ]
        );
      }
    }

    public function keys()
    {
      return ['MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_STATUS',
        'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_CONTENT_WIDTH',
        'MODULE_ADMIN_DASHBOARD_WHOS_ONLINE_APP_SORT_ORDER'
      ];
    }
  }
