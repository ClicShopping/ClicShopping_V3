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

  class TotalCaByYear extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {

    protected $lang;
    protected $app;

    protected function init()
    {

      if (!Registry::exists('Orders')) {
        Registry::set('Orders', new OrdersApp());
      }

      $this->app = Registry::get('Orders');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/total_ca_by_year');

      $this->title = $this->app->getDef('module_admin_dashboard_total_ca_by_year_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_total_ca_by_year_app_description');

      if (defined('MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_STATUS')) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_STATUS == 'True');
      }
    }

    public function getOutput()
    {

      $year = [];
      for ($i = 0; $i < 5; $i++) {
        $year[date('Y', strtotime('-' . $i . ' year'))] = 0;
      }

      $Qorder = $this->app->db->prepare('select date_format(o.date_purchased, "%Y") as year,
                                                 sum(ot.value) as total
                                          from :table_orders  o,
                                               :table_orders_total ot
                                          where  o.orders_id = ot.orders_id
                                          and o.orders_status = 3
                                          and (ot.class = :class or class = :class1)
                                          group by year
                                       ');
      $Qorder->bindValue(':class', 'ot_subtotal');
      $Qorder->bindValue(':class1', 'ST');
      $Qorder->execute();

      while ($Qorder->fetch()) {
        $year[$Qorder->value('year')] = $Qorder->value('total');
      }

      $year = array_reverse($year, true);

      $js_array = '';
      foreach ($year as $date => $total) {
        $js_array .= '[' . (mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4)) * 1000) . ', ' . $total . '],';
      }

      if (!empty($js_array)) {
        $js_array = substr($js_array, 0, -1);
      }

      $chart_label_link = CLICSHOPPING::link(null, 'A&Orders\Orders&Orders');
      $chart_title = HTML::output($this->app->getDef('module_admin_dashboard_total_ca_by_year_app_chart_link'));

      $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_CONTENT_WIDTH;

      $output = <<<EOD
<div class="{$content_width}">
  <div class="card-deck mb-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title"><i class="fa fa-coins"></i> {$chart_title}</h6>
        <p class="card-text"><div id="d_total_ca_by_year" class="col-md-12" style="width:100%; height: 200px;"></div></p>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(function () {
  var plot30 = [$js_array];
  $.plot($("#d_total_ca_by_year"), [ {
    label: '',
    data: plot30,
    bars: {
      show: true,
      fill: true,
      lineWidth: 20,
      barWidth: 20,
      align:  "center"
    },
    color: '#2a6ac4'
    }], {

    xaxis: {
      ticks: 4,
      mode: 'time'
    },

    yaxis: {
      ticks: 5,
      min: 0
    },

    grid: {
      backgroundColor: { colors:  ['#FAFAFA', '#FAFAFA'] }, //gradient ['#d3d3d3', '#fff']
      hoverable: true,
      borderWidth: 1
    },

    legend: {
      labelFormatter: function(label, series) {
        return '<a href="$chart_label_link">' + label + '</a>';
      }
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
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_STATUS',
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
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_SORT_ORDER',
          'configuration_value' => '30',
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
      return ['MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_STATUS',
        'MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_CONTENT_WIDTH',
        'MODULE_ADMIN_DASHBOARD_TOTAL_CA_BY_YEAR_APP_SORT_ORDER'
      ];
    }
  }
