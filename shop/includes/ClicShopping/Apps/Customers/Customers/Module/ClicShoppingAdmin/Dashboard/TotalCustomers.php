<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Customers\Customers\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Customers\Customers as CustomersApp;

  class TotalCustomers extends \ClicShopping\OM\Modules\AdminDashboardAbstract {

    protected $lang;
    protected $app;

    protected function init() {

      if (!Registry::exists('Customers')) {
        Registry::set('Customers', new CustomersApp());
      }

      $this->app = Registry::get('Customers');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/total_customers');

      $this->title = $this->app->getDef('module_admin_dashboard_total_customers_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_total_customers_app_description');

      if ( defined('MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS') ) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS == 'True');
      }
    }

    public function getOutput() {

      $days = [];
      for($i = 0; $i < 30; $i++) {
        $days[date('Y-m-d', strtotime('-'. $i .' days'))] = 0;
      }

      $Qorders = $this->app->db->query('select date_format(customers_info_date_account_created, "%Y-%m-%d") as dateday,
                                        count(*) as total
                                        from :table_customers_info
                                        where date_sub(curdate(), interval 30 day) <= customers_info_date_account_created
                                        group by dateday
                                      ');

      while ($Qorders->fetch()) {
        $days[$Qorders->value('dateday')] = $Qorders->value('total');
      }

      $days = array_reverse($days, true);

      $js_array = '';
      foreach ($days as $date => $total) {
        $js_array .= '[' . (mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4))*1000) . ', ' . $total . '],';
      }

      if (!empty($js_array)) {
        $js_array = substr($js_array, 0, -1);
      }

      $chart_label_link = CLICSHOPPING::link(null, 'Customers\Customers&Customers');
      $chart_tiltle = $this->app->getDef('module_admin_dashboard_total_customers_app_chart_link');

      $content_width = 'col-lg-' . (int)MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_CONTENT_WIDTH;

      $output = <<<EOD
<span class="$content_width text-md-center">
<div class="text-md-center">$chart_tiltle</div>
<div id="d_total_customers_app" class="col-md-12" style="width:100%; height: 200px;"></div>
<script type="text/javascript">
$(function () {
  var plot30 = [$js_array];



  $.plot($('#d_total_customers_app'), [ {
    label: '',
    data: plot30,
    lines: { show: true, fill: true },
    points: { show: true },
    color: '#faa09d'
  }], {
    xaxis: {
      ticks: 4,
      mode: 'time'
    },
    yaxis: {
      ticks: 3,
      min: 0
    },
    grid: {
      backgroundColor: { colors: ['#FAFAFA', '#FAFAFA'] }, //gradient ['#d3d3d3', '#fff']
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

function showTooltip(x, y, contents) {
  $('<div id="tooltip">' + contents + '</div>').css( {
    position: 'absolute',
    display: 'none',
    top: y + 5,
    left: x + 5,
    border: '1px solid #fdd',
    padding: '2px',
    backgroundColor: '#fee',
    opacity: 0.80
  }).appendTo('body').fadeIn(200);
}

var monthNames = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];

var previousPoint = null;
$('#d_total_customers_app').bind('plothover', function (event, pos, item) {
  if (item) {
    if (previousPoint != item.datapoint) {
      previousPoint = item.datapoint;

      $('#tooltip').remove();
      var x = item.datapoint[0],
          y = item.datapoint[1],
          xdate = new Date(x);

      showTooltip(item.pageX, item.pageY, y + ' for ' + monthNames[xdate.getMonth()] + '-' + xdate.getDate());
    }
  } else {
    $('#tooltip').remove();
    previousPoint = null;
  }
});
</script>
</span>

EOD;

      return $output;
    }

    public function Install() {
      if ($this->lang->getId() != 2) {
        $this->app->db->save('configuration', [
            'configuration_title' => 'Souhaitez vous activer ce module ?',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS',
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
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_CONTENT_WIDTH',
            'configuration_value' => '6',
            'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
            'configuration_group_id' => '6',
            'sort_order' => '1',
            'set_function' => 'clic_cfg_set_content_module_width_pull_down',
            'date_added' => 'now()'
          ]
        );

        $this->app->db->save('configuration', [
            'configuration_title' => 'Veuillez choisir votre interval d\'analyse',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_INTERVAL',
            'configuration_value' => '30 Day',
            'configuration_description' => 'interval d\'analyse',
            'configuration_group_id' => '6',
            'sort_order' => '1',
            'set_function' => 'clic_cfg_set_boolean_value(array(\'7 Day\', \'14 Day\', \'30 Day\', \'90 Day\', \'182 Day\', \'365 Day\'))',
            'date_added' => 'now()'
          ]
        );

        $this->app->db->save('configuration', [
            'configuration_title' => 'Ordre de tri d\'affichage',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_SORT_ORDER',
            'configuration_value' => '45',
            'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
            'configuration_group_id' => '6',
            'sort_order' => '99',
            'set_function' => '',
            'date_added' => 'now()'
          ]
        );

      } else {

        $this->app->db->save('configuration', [
            'configuration_title' => 'Do you want to enable this Module ?',
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS',
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
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_CONTENT_WIDTH',
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
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_INTERVAL',
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
            'configuration_key' => 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_SORT_ORDER',
            'configuration_value' => '45',
            'configuration_description' => 'Sort order of display. Lowest is displayed first.',
            'configuration_group_id' => '6',
            'sort_order' => '2',
            'set_function' => '',
            'date_added' => 'now()'
          ]
        );
      }
    }

    public function keys() {
      return ['MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS',
               'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_CONTENT_WIDTH',
               'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_INTERVAL',
               'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_SORT_ORDER'
              ];
    }
  }
