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

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class SumTotalTokenByMonth extends \ClicShopping\OM\Modules\AdminDashboardAbstract
{
  protected mixed $lang;
  private mixed $app;
  public $group;

  protected function init()
  {
    if (!Registry::exists('ChatGpt')) {
      Registry::set('ChatGpt', new ChatGptApp());
    }

    $this->app = Registry::get('ChatGpt');
    $this->lang = Registry::get('Language');

    $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/sum_total_token');

    $this->title = $this->app->getDef('module_admin_dashboard_sum_total_gpt_token_app_title');
    $this->description = $this->app->getDef('module_admin_dashboard_sum_total_gpt_token_app_description');

    if (\defined('MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_STATUS')) {
      $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_SORT_ORDER ?? 0;
      $this->enabled = (MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_STATUS == 'True');
    }
  }

  public function getOutput(): string
  {
    $months = [];
    for ($i = 0; $i < 12; $i++) {
      $months[date('Y-m', strtotime("-$i months"))] = 0;
    }

    $Qorders = $this->app->db->query('select date_format(date_added, "%Y-%m") as datemonth,
                                        sum(totalTokens) as total
                                        from :table_gpt_usage
                                        where date_sub(curdate(), interval 12 month) <= date_added
                                        group by datemonth
                                      ');

    while ($Qorders->fetch()) {
      $months[$Qorders->value('datemonth')] = $Qorders->value('total');
    }

    $months = array_reverse($months, true);

    $data_labels = json_encode(array_keys($months));
    $data = json_encode(array_values($months));

//    $chart_label_link = HTML::link('index.php?A&Configuration\ChatGpt&ChatGpt', $this->app->getDef('module_admin_dashboard_sum_total_gpt_token_app_chart_link'));
    $chart_label_link = $this->app->getDef('module_admin_dashboard_sum_total_gpt_token_app_chart_link');

    $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_CONTENT_WIDTH;

    $output = <<<EOD
<div class="col-12 {$content_width} d-flex" style="padding-right:0.5rem; padding-top:0.5rem">
  <div class="card flex-fill h-215">
    <div class="card-block">
      <div class="card-body">
        <h6 class="card-title"><i class="bi bi-graph-up"></i> {$chart_label_link}</h6>
        <p class="card-text">
          <div class="col-md-12">
            <canvas id="d_total_gpt_sum_token_app" class="col-md-12" style="display: block; width:100%; height: 215px;"></canvas>
          </div>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
var ctx = document.getElementById('d_total_gpt_sum_token_app');
var myChart = new Chart(ctx, {
    type: 'line', // Change 'bar' to 'line'
    data: {
        labels: $data_labels,
        datasets: [{
            label: 'Gpt Token',
            data: $data,
            backgroundColor: 'rgba(255, 0, 255, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)', // Change alpha value to 1 for solid line
            borderWidth: 1 // Increase border width for better visibility
        }]
    },
    options: {
        maintainAspectRatio: true,
        legend: {
          display: false
        },        
        scales: {
            y: {
                beginAtZero: true
            }
        },
        x: { // Change xAxes to x
          reverse: true,
          gridLines: {
            color: "rgba(0,0,0,0.05)"
          }
        },
        y: { // Change yAxes to y
          ticks: {
            stepSize: 1
          },
          display: true,
          borderDash: [5, 5],
          gridLines: {
            color: "rgba(0,0,0,0.050)",
            fontColor: "#fff"
          }
        }
    }
});

function beforePrintHandler() {
    for (var id in Chart.instances) {
        Chart.instances[id].resize();
    }
}
</script>
EOD;
    return $output;
  }


  public function Install()
  {
    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to enable this Module ?',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_STATUS',
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
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_CONTENT_WIDTH',
        'configuration_value' => '6',
        'configuration_description' => 'Select a number between 1 to 12',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_content_module_width_pull_down',
        'date_added' => 'now()'
      ]
    );

    $this->app->db->save('configuration', [
        'configuration_title' => 'Choose your analyse interval ?',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_INTERVAL',
        'configuration_value' => '30 Day',
        'configuration_description' => 'Analyse interval',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_boolean_value(array(\'7 Day\', \'14 Day\', \'30 Day\', \'90 Day\', \'182 Day\', \'365 Day\'))',
        'date_added' => 'now()'
      ]
    );

    $this->app->db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_SORT_ORDER',
        'configuration_value' => '45',
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
      'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_STATUS',
      'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_CONTENT_WIDTH',
      'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_INTERVAL',
      'MODULE_ADMIN_DASHBOARD_SUM_TOTAL_GPT_TOKEN_APP_SORT_ORDER'
    ];
  }
}
