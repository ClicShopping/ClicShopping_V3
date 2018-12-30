<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Tools\ActionsRecorder\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Tools\ActionsRecorder\ActionsRecorder as ActionsRecorderApp;

  class AdminLogins extends \ClicShopping\OM\Modules\AdminDashboardAbstract {

    protected $lang;
    protected $app;

    protected function init() {

      if (!Registry::exists('ActionsRecorder')) {
        Registry::set('ActionsRecorder', new ActionsRecorderApp());
      }

      $this->app = Registry::get('ActionsRecorder');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/admin_logins');

      $this->title = $this->app->getDef('module_admin_dashboard_admin_logins_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_admin_logins_app_description');

      if ( defined('MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_STATUS == 'True');
      }
    }

    public function getOutput() {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $content_width = (int)MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_CONTENT_WIDTH;

      $output = '<div class="col-md-' . $content_width . '">';
      $output .= '<div class="separator"></div>';
      $output .= '<table class="table table-sm table-hover">' .
                '<thead>' .
                '  <tr class="dataTableHeadingRow">' .
                '    <th width="20">&nbsp;</th>' .
                '    <th>' . $this->app->getDef('module_admin_dashboard_admin_heading_title') . '</th>' .
                '    <th class="text-md-center">' . $this->app->getDef('module_admin_dashboard_admin_logins_app_date') . '</th>' .
                '    <th class="text-md-right">' . $this->app->getDef('module_admin_dashboard_admin_logins_action') . '</th>' .
                '  </tr>' .
                '</thead>';
                '<tbody>';

      $Qlogins = $this->app->db->get('action_recorder', [
                                                      'id',
                                                      'user_name',
                                                      'success',
                                                      'date_added'
                                                    ], [
                                                      'module' => 'ar_admin_login'
                                                    ],
                                                    'date_added desc',
                                                    6
                                );

      while ($Qlogins->fetch()) {
        $output .= '  <tr>' .
                   '    <td class="text-md-center"><i class="fas fa-' . (($Qlogins->valueInt('success') === 1) ? 'check fa-lg' : 'times fa-lg') . '"></i>&nbsp;' .
                   '    <td><a href="' . CLICSHOPPING::link(null, 'A&Tools\ActionsRecorder&ActionsRecorder&module=ar_admin_login&aID=' . $Qlogins->valueInt('id')) . '">' . $Qlogins->valueProtected('user_name') . '</a></td>' .
                   '    <td class="text-md-center">' . DateTime::toShort($Qlogins->value('date_added')) . '</td>' .
                   '    <td class="text-md-right"><a href="' . CLICSHOPPING::link(null, 'A&Tools\ActionsRecorder&ActionsRecorder&module=ar_admin_login&aID=' . $Qlogins->valueInt('id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $this->app->getDef('module_admin_dashboard_admin_logins_icon_edit')) . '</a>&nbsp;' .
                   '  </tr>';
      }

      $output .= '<tbody>';
      $output .= '</table>';
      $output .= '</div>';
      $output .= '<div class="separator"></div>';

      return $output;
    }

    public function Install() {

        $this->app->db->save('configuration', [
          'configuration_title' => 'Enable Administrator Logins Module',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to display the latest administrator logins on the dashboard?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

         $this->app->db->save('configuration', [
          'configuration_title' => 'Select the width to display',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_SORT_ORDER',
          'configuration_value' => '400',
          'configuration_description' => 'Sort order of display. Lowest is displayed first.',
          'configuration_group_id' => '6',
          'sort_order' => '0',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys() {
      return ['MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_STATUS',
              'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_APP_SORT_ORDER'
             ];
    }
  }
