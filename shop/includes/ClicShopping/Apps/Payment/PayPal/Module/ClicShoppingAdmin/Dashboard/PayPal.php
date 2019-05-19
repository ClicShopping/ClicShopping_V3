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

  namespace ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Dashboard;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\PayPal\PayPal as PayPalApp;

  class PayPal extends \ClicShopping\OM\Modules\AdminDashboardAbstract
  {
    protected $app;

    protected function init()
    {
      if (!Registry::exists('PayPal')) {
        Registry::set('PayPal', new PayPalApp());
      }

      $this->app = Registry::get('PayPal');

      $this->app->loadDefinitions('ClicShoppingAdmin/balance');
      $this->app->loadDefinitions('ClicShoppingAdmin/modules/dashboard/d_paypal_app');

      $this->title = $this->app->getDef('module_admin_dashboard_title');
      $this->description = $this->app->getDef('module_admin_dashboard_description');

      if (defined('MODULE_ADMIN_DASHBOARD_PAYPAL_APP_SORT_ORDER')) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_PAYPAL_APP_SORT_ORDER;
        $this->enabled = true;
      }
    }

    public function getOutput()
    {
      $has_live_account = ($this->app->hasApiCredentials('live') === true) ? 'true' : 'false';
      $has_sandbox_account = ($this->app->hasApiCredentials('sandbox') === true) ? 'true' : 'false';
      $heading_live_account = $this->app->getDef('heading_live_account', ['account' => str_replace('_api1.', '@', $this->app->getApiCredentials('live', 'username'))]);
      $heading_sandbox_account = $this->app->getDef('heading_sandbox_account', ['account' => str_replace('_api1.', '@', $this->app->getApiCredentials('sandbox', 'username'))]);
      $cached_notice = $this->app->getDef('cached_notice');
      $receiving_balance_progress = $this->app->getDef('retrieving_balance_progress');
      $app_get_started = HTML::button($this->app->getDef('button_app_get_started'), null, $this->app->link(), 'primary');
      $error_balance_retrieval = addslashes($this->app->getDef('error_balance_retrieval'));
      $get_balance_url = addslashes($this->app->link('RPC&GetBalance&type=PPTYPE'));

      $content_width = 'col-lg-' . (int)MODULE_ADMIN_DASHBOARD_PAYPAL_APP_CONTENT_WIDTH;

      $output = <<<EOD
        <span class="$content_width text-md-center">
<script>
var CLICSHOPPING = {
  htmlSpecialChars: function(string) {
    if ( string == null ) {
      string = '';
    }

    return $('<span />').text(string).html();
  },
  APP: {
    PAYPAL: {
      accountTypes: {
        live: {$has_live_account},
        sandbox: {$has_sandbox_account}
      }
    }
  }
};
</script>

<div id="ppAccountBalanceLive" class="panel panel-success">
  <div class="panel-heading">
    <h3 class="panel-title">{$heading_live_account} <span class="small float-md-right">{$cached_notice}</span></h3>
  </div>

  <div id="ppBalanceLiveInfo" class="panel-body">
    <p>{$receiving_balance_progress}</p>
  </div>
</div>

<div id="ppAccountBalanceSandbox" class="panel panel-warning">
  <div class="panel-heading">
    <h3 class="panel-title">{$heading_sandbox_account} <span class="small float-md-right">{$cached_notice}</span></h3>
  </div>

  <div id="ppBalanceSandboxInfo" class="panel-body">
    <p>{$receiving_balance_progress}</p>
  </div>
</div>

<div id="ppAccountBalanceNone" class="panel panel-primary" style="display: none;">
  <div class="panel-heading">
    <h3 class="panel-title">PayPal</h3>
  </div>

  <div class="panel-body">
    <p>{$app_get_started}</p>
  </div>
</div>

<script>
CLICSHOPPING.APP.PAYPAL.getBalance = function(type) {
  var def = {
    'error_balance_retrieval': '{$error_balance_retrieval}'
  };

  var divId = 'ppBalance' + type.charAt(0).toUpperCase() + type.slice(1) + 'Info';

  $.get('{$get_balance_url}'.replace('PPTYPE', type), function (data) {
    var balance = {};

    $('#' + divId).empty();

    try {
      data = $.parseJSON(data);
    } catch (ex) {
    }

    if ( (typeof data == 'object') && ('rpcStatus' in data) && (data['rpcStatus'] == 1) ) {
      if ( ('balance' in data) && (typeof data['balance'] == 'object') ) {
        balance = data['balance'];
      }
    } else if ( (typeof data == 'string') && (data.indexOf('rpcStatus') > -1) ) {
      var result = data.split("\\n", 1);

      if ( result.length == 1 ) {
        var rpcStatus = result[0].split('=', 2);

        if ( rpcStatus[1] == 1 ) {
          var entries = data.split("\\n");

          for ( var i = 0; i < entries.length; i++ ) {
            var entry = entries[i].split('=', 2);

            if ( (entry.length == 2) && (entry[0] != 'rpcStatus') ) {
              balance[entry[0]] = entry[1];
            }
          }
        }
      }
    }

    var pass = false;

    for ( var key in balance ) {
      pass = true;

      $('#' + divId).append('<p><strong>' + CLICSHOPPING.htmlSpecialChars(key) + ':</strong> ' + CLICSHOPPING.htmlSpecialChars(balance[key]) + '</p>');
    }

    if ( pass == false ) {
      $('#' + divId).append('<p>' + def['error_balance_retrieval'] + '</p>');
    }
  }).fail(function() {
    $('#' + divId).empty().append('<p>' + def['error_balance_retrieval'] + '</p>');
  });
};

$(function() {
  (function() {
    var pass = false;

    if ( CLICSHOPPING.APP.PAYPAL.accountTypes['live'] == true ) {
      pass = true;

      $('#ppAccountBalanceSandbox').hide();

      CLICSHOPPING.APP.PAYPAL.getBalance('live');
    } else {
      $('#ppAccountBalanceLive').hide();

      if ( CLICSHOPPING.APP.PAYPAL.accountTypes['sandbox'] == true ) {
        pass = true;

        CLICSHOPPING.APP.PAYPAL.getBalance('sandbox');
      } else {
        $('#ppAccountBalanceSandbox').hide();
      }
    }

    if ( pass == false ) {
      $('#ppAccountBalanceNone').show();
    }
  })();
});
</script>
</span>
EOD;

      return $output;
    }

    public function install()
    {
      $this->app->db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_PAYPAL_APP_SORT_ORDER',
        'configuration_value' => '70',
        'configuration_description' => 'Sort order of display. Lowest is displayed first.',
        'configuration_group_id' => '6',
        'sort_order' => '0',
        'date_added' => 'now()'
      ]);

      $this->app->db->save('configuration', [
          'configuration_title' => 'Select the width to display',
          'configuration_key' => 'MODULE_ADMIN_DASHBOARD_PAYPAL_APP_CONTENT_WIDTH',
          'configuration_value' => '6',
          'configuration_description' => 'Select a number between 1 to 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );
    }


    public function keys()
    {
      return [
        'MODULE_ADMIN_DASHBOARD_PAYPAL_APP_SORT_ORDER',
        'MODULE_ADMIN_DASHBOARD_PAYPAL_APP_CONTENT_WIDTH'
      ];
    }
  }
