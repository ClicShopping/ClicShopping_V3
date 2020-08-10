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

  namespace ClicShopping\Apps\Customers\Customers\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Customers\Customers as CustomersApp;

  class TotalCustomers extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {
    protected $lang;
    protected $app;
    public $group;

    protected function init()
    {
      if (!Registry::exists('Customers')) {
        Registry::set('Customers', new CustomersApp());
      }

      $this->app = Registry::get('Customers');
      $this->lang = Registry::get('Language');

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Dashboard/total_customers');

      $this->title = $this->app->getDef('module_admin_dashboard_total_customers_app_title');
      $this->description = $this->app->getDef('module_admin_dashboard_total_customers_app_description');

      if (defined('MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS')) {
        $this->sort_order = (int)MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS == 'True');
      }
    }

    public function getOutput()
    {
      $days = [];
      for ($i = 0; $i < 15; $i++) {
        $days[date('d', strtotime('-' . $i . ' days'))] = 0;
      }

      $Qorders = $this->app->db->query('select date_format(customers_info_date_account_created, "%d") as dateday,
                                        count(customers_info_id) as total
                                        from :table_customers_info
                                        where date_sub(curdate(), interval 7 day) <= customers_info_date_account_created
                                        group by dateday
                                      ');

      while ($Qorders->fetch()) {
        $days[$Qorders->value('dateday')] = $Qorders->value('total');
      }

      $days = array_reverse($days, true);

      $data_labels = json_encode(array_keys($days));
      $data = json_encode(array_values($days));

      $chart_label_link = HTML::link('index.php?A&Customers\Customers&Customers', $this->app->getDef('module_admin_dashboard_total_customers_app_chart_link'));

      $content_width = 'col-md-' . (int)MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_CONTENT_WIDTH;

      $output = <<<EOD
<div class="{$content_width}">
  <div class="card-deck mb-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title"><i class="fa fa-female"></i> {$chart_label_link}</h6>
        <p class="card-text"><div id="d_total_customers_app" class="col-md-12" style="width:100%; height: 200px;"></div></p>
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

  var chart = new Chartist.Bar('#d_total_customers_app', data, options);

  chart.on('draw', function(context) {
    if (context.type === 'bar') {
      context.element.attr({
        style: 'stroke: #FAA09D; stroke-width: 20px'
    
      });
    } else if (context.type === 'area') {
      context.element.attr({
        style: 'fill: blue;'
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

    public function keys()
    {
      return ['MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_STATUS',
        'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_CONTENT_WIDTH',
        'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_INTERVAL',
        'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_APP_SORT_ORDER'
      ];
    }
  }
