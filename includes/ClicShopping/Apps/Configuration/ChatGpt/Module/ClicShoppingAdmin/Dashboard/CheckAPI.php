<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Dashboard;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;

class CheckAPI extends \ClicShopping\OM\Modules\AdminDashboardAbstract
{
  protected mixed $lang;
  protected mixed $app;
  public $group;

  protected function init()
  {
    if (!Registry::exists('ChatGpt')) {
      Registry::set('ChatGpt', new ChatGptApp());
    }

    $this->app = Registry::get('ChatGpt');
    $this->lang = Registry::get('Language');

    $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/check_api');

    $this->title = $this->app->getDef('module_admin_dashboard_check_api_app_title');
    $this->description = $this->app->getDef('module_admin_dashboard_total_check_api_app_description');

    if (\defined('MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_STATUS')) {
      $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_SORT_ORDER ?? 0;
      $this->enabled = (MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_STATUS == 'True');
    }
  }

  public function getOutput(): string
  {
    $output = '';

    if(empty(CLICSHOPPING_APP_CHATGPT_CH_API_KEY)) {
      $link = HTML::link( $this->app ->link('Configuration\ChatGpt&Configure'), $this->app->getDef('module_admin_dashboard_check_api_app_link'));

      $output = '<div class="col-md-' . (int)MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_CONTENT_WIDTH . '">';
      $output .= '<div class="alert alert-warning" role="alert">';
      $output .= $this->app->getDef('module_admin_dashboard_check_api_app_alert', ['gpt_link' => $link]);
      $output .= '</div>';
      $output .= '</div>';
   }

    return $output;
  }

  public function Install()
  {
    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to enable this Module ?',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_STATUS',
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
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_CONTENT_WIDTH',
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
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_SORT_ORDER',
        'configuration_value' => '2',
        'configuration_description' => 'Sort order of display. Lowest is displayed first.',
        'configuration_group_id' => '6',
        'sort_order' => '2',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  public function keys(): array
  {
    return [
      'MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_STATUS',
      'MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_CONTENT_WIDTH',
      'MODULE_ADMIN_DASHBOARD_GPT_CHECK_API_APP_SORT_ORDER'
    ];
  }
}
