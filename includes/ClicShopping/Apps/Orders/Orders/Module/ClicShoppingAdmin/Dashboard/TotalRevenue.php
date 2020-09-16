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

  namespace ClicShopping\Apps\Orders\Orders\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Orders\Orders\Orders as OrdersApp;

  class TotalRevenue extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {
    protected $lang;
    protected $app;
    public $group;

    protected function init()
    {
      if (!Registry::exists('Orders')) {
        Registry::set('Orders', new OrdersApp());
      }

      $this->app = Registry::get('Orders');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/total_revenue');

      $this->title = $this->app->getDef('module_admin_dashboard_total_revenue_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_total_revenue_app_description');

      if (defined('MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_STATUS')) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_STATUS == 'True');
      }
    }

    public function getOutput()
    {
      $month = [];

      for ($i = 0; $i <= 12; $i++) {
        $month[date('M')] = 0;
      }

      $Qorder = $this->app->db->query('select date_format(o.date_purchased, "%M") as dateday,
                                             sum(ot.value) as total
                                      from :table_orders o,
                                           :table_orders_total ot
                                      where date_sub(now(), interval 1 year) <= o.date_purchased
                                      and (o.orders_status > 0 and o.orders_status <> 4)
                                      and o.orders_id = ot.orders_id
                                      and ot.class = "ST"
                                      group by dateday
                                     ');

      while ($Qorder->fetch()) {
        $month[$Qorder->value('dateday')] = $Qorder->value('total');
      }

      $month = array_reverse($month, true);

      $data_labels = json_encode(array_keys($month));
      $data = json_encode(array_values($month));

      $chart_label_link = HTML::link('index.php?A&Orders\Orders&Orders', $this->app->getDef('module_admin_dashboard_total_revenue_app_chart_link'));

      $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_CONTENT_WIDTH;

      $output = <<<EOD
<div class="{$content_width}">
  <div class="card-deck mb-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title"><i class="fa fa-coins"></i>{$chart_label_link}</h6>
        <p class="card-text"><div id="d_total_revenue" class="col-md-12" style="width:100%; height: 200px;"></div></p>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function() {
  var data = {
    labels: $data_labels,
    series: [ $data ]
  };

  var options = {
    fullWidth: true,
    height: '250px',
    showPoint: false,
    showArea: true,
    axisY: {
      labelInterpolationFnc: function skipLabels(value, index) {
        return index % 2  === 0 ? value : null;
      }
    }
  }

  var chart = new Chartist.Bar('#d_total_revenue', data, options);

  chart.on('draw', function(context) {
    if (context.type === 'bar') {
      context.element.attr({
        style: 'stroke: #13bf4c; stroke-width: 40px'
    
      });
    } else if (context.type === 'area') {
      context.element.attr({
        style: 'fill: #13bf4c;'
      });
    }
  });
});
</script>
EOD;

      return $output;
    }

    public function Install()
    {

      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to enable this Module ?',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_STATUS',
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
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_CONTENT_WIDTH',
          'configuration_value' => '6',
          'configuration_description' => 'Select a number between 1 to 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $this->app->db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_SORT_ORDER',
          'configuration_value' => '40',
          'configuration_description' => 'Sort order of display. Lowest is displayed first.',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys()
    {
      return ['MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_STATUS',
        'MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_CONTENT_WIDTH',
        'MODULE_ADMIN_DASHBOARD_TOTAL_REVENUE_APP_SORT_ORDER'
      ];
    }
  }
