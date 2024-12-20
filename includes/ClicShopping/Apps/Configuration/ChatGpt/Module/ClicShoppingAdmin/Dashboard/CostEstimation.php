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

class CostEstimation extends \ClicShopping\OM\Modules\AdminDashboardAbstract
{
  private mixed $lang;
  public mixed $app;
  public $group;

  /**
   * Initializes the module by setting up the required application instance,
   * language configurations, and loading module definitions. It also determines
   * the title, description, sort order, and enabled status of the module based on defined constants.
   *
   * @return void
   */
  protected function init()
  {
    if (!Registry::exists('ChatGpt')) {
      Registry::set('ChatGpt', new ChatGptApp());
    }

    $this->app = Registry::get('ChatGpt');
    $this->lang = Registry::get('Language');

    $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/cost_estimation');

    $this->title = $this->app->getDef('module_admin_dashboard_total_cost_estimation_app_title');
    $this->description = $this->app->getDef('module_admin_dashboard_total_cost_estimation_app_description');

    if (\defined('MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_STATUS')) {
      $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_SORT_ORDER ?? 0;
      $this->enabled = (MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_STATUS == 'True');
    }
  }

  /**
   * Generates and returns the HTML and JavaScript output for a dashboard widget that displays
   * a bar chart of GPT token usage and costs over the past 12 months.
   *
   * The method retrieves token usage data from the database, calculates costs based on the
   * application price configuration, structures the data into JSON format for use in a JavaScript chart,
   * and generates the necessary HTML and chart configuration.
   *
   * @return string The complete HTML and JavaScript content for rendering the dashboard widget.
   */
  public function getOutput(): string
  {
    $price = MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_PRICE;

    $Qorders = $this->app->db->query("SELECT DATE_FORMAT(date_added, '%Y-%m') as month, 
                                      SUM(totalTokens) as total
                                      FROM :table_gpt_usage
                                      WHERE date_sub(curdate(), interval 12 month) <= date_added
                                      GROUP BY month 
                                      ORDER BY month desc
                                    ");

    $months = [];

    while ($Qorders->fetch()) {
      $totalTokens = $Qorders->valueInt('total');
      $months[$Qorders->value('month')] = ($totalTokens / 100) * $price;
    }

    $months = array_reverse($months, true);

    $data_labels = json_encode(array_keys($months));
    $data = json_encode(array_values($months));


    //$chart_label_link = HTML::link('index.php?A&Configuration\ChatGpt&ChatGpt', $this->app->getDef('module_admin_dashboard_total_cost_estimation_app_chart_link'));
    $chart_label_link = $this->app->getDef('module_admin_dashboard_total_cost_estimation_app_chart_link');

    $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_CONTENT_WIDTH;

$output = <<<EOD
<div class="col-12 {$content_width} d-flex" style="padding-right:0.5rem; padding-top:0.5rem">
  <div class="card flex-fill h-215">
    <div class="card-block">
      <div class="card-body">
        <h6 class="card-title"><i class="bi bi-graph-up"></i> {$chart_label_link}</h6>
        <p class="card-text">
          <div class="col-md-12">
            <canvas id="d_total_cost_estimation_app" class="col-md-12" style="display: block; width:100%; height: 215px;"></canvas>
          </div>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
var ctx = document.getElementById('d_total_cost_estimation_app');
var myChart = new Chart(ctx, {
    type: 'bar', // Change 'bar' to 'line'
    data: {
        labels: $data_labels,
        datasets: [{
            label: 'Gpt Token',
            data: $data,
            backgroundColor: 'rgba(0, 0, 255, 0.2)',
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

function beforePrintHandler () {
    for (var id in Chart.instances) {
        Chart.instances[id].resize();
    }
}
</script>
EOD;
    return $output;
  }

  /**
   * Installs the module by saving necessary configuration settings to the database.
   *
   * @return void
   */
  public function Install()
  {
    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to enable this Module ?',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_STATUS',
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
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_CONTENT_WIDTH',
        'configuration_value' => '6',
        'configuration_description' => 'Select a number between 1 to 12',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_content_module_width_pull_down',
        'date_added' => 'now()'
      ]
    );

    $this->app->db->save('configuration', [
        'configuration_title' => 'Do you want to enable this Module ?',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_PRICE',
        'configuration_value' => '0.001',
        'configuration_description' => 'Sort order of display. Lowest is displayed first.',
        'configuration_group_id' => '6',
        'sort_order' => '2',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );

    $this->app->db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_SORT_ORDER',
        'configuration_value' => '50',
        'configuration_description' => 'Sort order of display. Lowest is displayed first.',
        'configuration_group_id' => '6',
        'sort_order' => '2',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  /**
   * Retrieves a list of configuration keys related to the dashboard total cost estimation module.
   *
   * @return array An array of configuration keys used by the total cost estimation module.
   */
  public function keys(): array
  {
    return [
      'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_STATUS',
      'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_CONTENT_WIDTH',
      'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_PRICE',
      'MODULE_ADMIN_DASHBOARD_TOTAL_COST_ESTIMATION_APP_SORT_ORDER'
    ];
  }
}
