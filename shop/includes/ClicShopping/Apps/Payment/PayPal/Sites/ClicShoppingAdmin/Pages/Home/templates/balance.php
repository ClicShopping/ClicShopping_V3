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

  require_once(__DIR__ . '/template_top.php');
?>

  <div class="card card-success" id="ppAccountBalanceLive">
    <div class="card-header">
      <?php echo $CLICSHOPPING_PayPal->getDef('heading_live_account', ['account' => str_replace('_api1.', '@', $CLICSHOPPING_PayPal->getApiCredentials('live', 'username'))]); ?>
    </div>
    <div class="card-block">
      <p class="card-text"><?php echo $CLICSHOPPING_PayPal->getDef('retrieving_balance_progress'); ?></p>
    </div>
  </div>

  <div class="card card-warning" id="ppAccountBalanceSandbox">
    <div class="card-header">
      <?php echo $CLICSHOPPING_PayPal->getDef('heading_sandbox_account', ['account' => str_replace('_api1.', '@', $CLICSHOPPING_PayPal->getApiCredentials('sandbox', 'username'))]); ?>
    </div>
    <div class="card-block">
      <p class="card-text">
      <p><?php echo $CLICSHOPPING_PayPal->getDef('retrieving_balance_progress'); ?></p>
    </div>
  </div>

  <div class="card card-danger" id="ppAccountBalanceNone" style="display: none;">
    <div class="card-block">
      <p class="card-text">
      <p><?php echo $CLICSHOPPING_PayPal->getDef('error_no_accounts_configured'); ?></p>
    </div>
  </div>

  <script>
      CLICSHOPPING.APP.PAYPAL.getBalance = function (type) {
          var def = {
              'error_balance_retrieval': '<?php echo addslashes($CLICSHOPPING_PayPal->getDef('error_balance_retrieval')); ?>'
          };

          var divId = 'ppAccountBalance' + type.charAt(0).toUpperCase() + type.slice(1);

          $.get('<?php echo addslashes($CLICSHOPPING_PayPal->link('RPC&GetBalance&type=PPTYPE&force=true')); ?>'.replace('PPTYPE', type), function (data) {
              var balance = {};

              $('#' + divId + ' .card-text').empty();

              try {
                  data = $.parseJSON(data);
              } catch (ex) {
              }

              if ((typeof data == 'object') && ('rpcStatus' in data) && (data['rpcStatus'] == 1)) {
                  if (('balance' in data) && (typeof data['balance'] == 'object')) {
                      balance = data['balance'];
                  }
              } else if ((typeof data == 'string') && (data.indexOf('rpcStatus') > -1)) {
                  var result = data.split("\n", 1);

                  if (result.length == 1) {
                      var rpcStatus = result[0].split('=', 2);

                      if (rpcStatus[1] == 1) {
                          var entries = data.split("\n");

                          for (var i = 0; i < entries.length; i++) {
                              var entry = entries[i].split('=', 2);

                              if ((entry.length == 2) && (entry[0] != 'rpcStatus')) {
                                  balance[entry[0]] = entry[1];
                              }
                          }
                      }
                  }
              }

              var pass = false;

              for (var key in balance) {
                  pass = true;

                  $('#' + divId + ' .card-text').append('<p><strong>' + CLICSHOPPING.htmlSpecialChars(key) + ':</strong> ' + CLICSHOPPING.htmlSpecialChars(balance[key]) + '</p>');
              }

              if (pass == false) {
                  $('#' + divId + ' .card-text').append('<p>' + def['error_balance_retrieval'] + '</p>');
              }
          }).fail(function () {
              $('#' + divId + ' .card-text').empty().append('<p>' + def['error_balance_retrieval'] + '</p>');
          });
      };

      $(function () {
          (function () {
              var pass = false;

              for (var key in CLICSHOPPING.APP.PAYPAL.accountTypes) {
                  if (CLICSHOPPING.APP.PAYPAL.accountTypes[key] === true) {
                      pass = true;

                      CLICSHOPPING.APP.PAYPAL.getBalance(key);
                  } else {
                      $('#ppAccountBalance' + key.charAt(0).toUpperCase() + key.slice(1)).hide();
                  }
              }

              if (pass == false) {
                  $('#ppAccountBalanceNone').show();
              }
          })();
      });
  </script>

<?php
  require_once(__DIR__ . '/template_bottom.php');