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

namespace ClicShopping\Apps\Payment\PayPal\Module\Hooks\ClicShoppingAdmin\Orders;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\PayPal\PayPal as PayPalApp;

  class PageContentTab implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()  {
      if (!Registry::exists('PayPal')) {
          Registry::set('PayPal', new PayPalApp());
      }

      $this->app = Registry::get('PayPal');
    }

    public function display()  {
        $oID = HTML::sanitize($_GET['oID']);

        if (!defined('CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID')) {
            return false;
        }

        $this->app->loadDefinitions('hooks/ClicShoppingAdmin/orders/tab');

        $output = '';

        $status = [];

        $Qc = $this->app->db->prepare('select comments
                                      from :table_orders_status_history
                                      where orders_id = :orders_id
                                      and orders_status_id = :orders_status_id
                                      and comments
                                      like "Transaction ID:%"
                                      order by date_added
                                      desc limit 1
                                     ');
        $Qc->bindInt(':orders_id', $oID);
        $Qc->bindInt(':orders_status_id', CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID);
        $Qc->execute();


        if ($Qc->fetch() !== false) {
            foreach (explode("\n", $Qc->value('comments')) as $s) {
              if (!empty($s) && (strpos($s, ':') !== false)) {
                $entry = explode(':', $s, 2);

                $status[trim($entry[0])] = trim($entry[1]);
              }
            }

            if (isset($status['Transaction ID'])) {
                $Qorder = $this->app->db->prepare('select o.orders_id,
                                                          o.payment_method,
                                                          o.currency,
                                                          o.currency_value,
                                                          ot.value as total
                                                    from :table_orders o,
                                                         :table_orders_total ot
                                                    where o.orders_id = :orders_id
                                                    and o.orders_id = ot.orders_id
                                                    and ot.class = :ot_total
                                                    ');
                $Qorder->bindInt(':orders_id', $oID);
                $Qorder->bindvalue(':ot_total', 'TO');
                $Qorder->execute();
                $pp_server = (strpos(strtolower($Qorder->value('payment_method')), 'sandbox') !== false) ? 'sandbox' : 'live';

                $info_button = HTML::button($this->app->getDef('button_details'), 'fas fa-info-circle', CLICSHOPPING::link(null, 'A&Orders\Orders&Edit&oID=' . $oID . '&page=' . $_GET['page'] . '&tabaction=#getTransactionDetails'), 'primary');
                $capture_button = $this->getCaptureButton($status, $Qorder->toArray());
                $void_button = $this->getVoidButton($status, $Qorder->toArray());
                $refund_button = $this->getRefundButton($status, $Qorder->toArray());
                $paypal_button = HTML::button($this->app->getDef('button_view_at_paypal'), 'fab fa-paypal', 'https://www.' . ($pp_server == 'sandbox' ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . $status['Transaction ID'],  'info', ['newwindow' => 'blank']);

                $tab_title = addslashes($this->app->getDef('tab_title'));

                $output = <<<EOD
<div id="section_paypalAppPayPal_content" class="card">
  {$info_button} {$capture_button} {$void_button} {$refund_button} {$paypal_button}
</div>

<script>
$('#section_paypalAppPayPal_content').appendTo('#orderTabs .tab-content');
$('#orderTabs .nav-tabs').append('    <li class="nav-item"><a data-target="#section_paypalAppPayPal_content" role="tab" data-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>
EOD;

            }
        }

        return $output;
    }

    protected function getCaptureButton($status, $order)  {
        $output = '';

        if (($status['Pending Reason'] == 'authorization') || ($status['Payment Status'] == 'In-Progress')) {

            $Qv = $this->app->db->prepare('select comments
                                            from :table_orders_status_history
                                            where orders_id = :orders_id
                                            and orders_status_id = :orders_status_id
                                            and comments like "%PayPal App: Void (%" limit 1
                                            ');
            $Qv->bindInt(':orders_id', $order['orders_id']);
            $Qv->bindInt(':orders_status_id', CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID);
            $Qv->execute();

            if ($Qv->fetch() === false) {
                $capture_total = $this->app->formatCurrencyRaw($order['total'], $order['currency'], $order['currency_value']);

                $Qc = $this->app->db->prepare('select comments
                                              from :table_orders_status_history
                                              where orders_id = :orders_id
                                              and orders_status_id = :orders_status_id
                                              and comments like "PayPal App: Capture (%"
                                              ');
                $Qc->bindInt(':orders_id', $order['orders_id']);
                $Qc->bindInt(':orders_status_id', CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID);
                $Qc->execute();

                while ($Qc->fetch()) {
                    if (preg_match('/^PayPal App\: Capture \(([0-9\.]+)\)\n/', $Qc->value('comments'), $c_matches)) {
                        $capture_total -= $this->app->formatCurrencyRaw($c_matches[1], $order['currency'], 1);
                    }
                }

                if ($capture_total > 0) {
                    $output .= HTML::button($this->app->getDef('button_dialog_capture'), 'fas fa-check-circle', '#', ['params' => 'data-button="paypalButtonDoCapture"'],  'success' );

                    $dialog_title = HTML::outputProtected($this->app->getDef('dialog_capture_title'));
                    $dialog_body = $this->app->getDef('dialog_capture_body');
                    $field_amount_title = $this->app->getDef('dialog_capture_amount_field_title');
                    $field_last_capture_title = $this->app->getDef('dialog_capture_last_capture_field_title', [
                        'currency' => $order['currency']
                    ]);
                    $capture_link = CLICSHOPPING::link(null, 'A&Orders\Orders&Edit&oID=' . $order['orders_id'] . '&page=' . $_GET['page'] . '&tabaction=doCapture');
                    $capture_currency = $order['currency'];
                    $dialog_button_capture = $this->app->getDef('dialog_capture_button_capture');
                    $dialog_button_cancel = $this->app->getDef('dialog_capture_button_cancel');

                    $output .= <<<EOD
<div id="paypal-dialog-capture" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{$dialog_title}</h4>
      </div>

      <div class="modal-body">
        <form id="ppCaptureForm" action="{$capture_link}" method="post">
          <p>{$dialog_body}</p>

          <div class="form-group">
            <label for="ppCaptureAmount">{$field_amount_title}</label>

            <div class="input-group">
              <div class="input-group-addon">
                {$capture_currency}
              </div>

              <input type="text" name="ppCaptureAmount" value="{$capture_total}" id="ppCaptureAmount" class="form-control" />
            </div>
          </div>

          <div id="ppPartialCaptureInfo" class="checkbox" style="display: none;">
            <label>
              <input type="checkbox" name="ppCatureComplete" value="true" id="ppCaptureComplete" /> {$field_last_capture_title}
            </label>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button id="paypal-dialog-capture-button" type="button" class="btn btn-success">{$dialog_button_capture}</button>
        <button type="button" class="btn btn-warning" data-dismiss="modal">{$dialog_button_cancel}</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
  $('a[data-button="paypalButtonDoCapture"]').click(function(e) {
    e.preventDefault();

    $('#paypal-dialog-capture').modal('show');
  });

  $('#paypal-dialog-capture-button').on('click', function() {
    $('#ppCaptureForm').submit();
  });

  (function() {
    var ppCaptureTotal = {$capture_total};

    $('#ppCaptureAmount').on('keyup', function() {
      if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
        this.value = this.value.replace(/[^0-9\.]/g, '');
      }

      if ( this.value < ppCaptureTotal ) {
        $('#ppCaptureVoidedValue').text((ppCaptureTotal - this.value).toFixed(2));
        $('#ppPartialCaptureInfo').show();
      } else {
        $('#ppPartialCaptureInfo').hide();
      }
    });
  })();
});
</script>
EOD;
                }
            }
        }

        return $output;
    }

    protected function getVoidButton($status, $order)
    {
        $output = '';

        if ($status['Pending Reason'] == 'authorization') {
            $Qv = $this->app->db->prepare('select comments
                                          from :table_orders_status_history
                                          where orders_id = :orders_id
                                          and orders_status_id = :orders_status_id
                                          and comments
                                          like "%PayPal App: Void (%"
                                          limit 1
                                          ');
            $Qv->bindInt(':orders_id', $order['orders_id']);
            $Qv->bindInt(':orders_status_id', CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID);
            $Qv->execute();

            if ($Qv->fetch() === false) {
                $capture_total = $this->app->formatCurrencyRaw($order['total'], $order['currency'], $order['currency_value']);

                $Qc = $this->app->db->prepare('select comments
                                              from :table_orders_status_history
                                              where orders_id = :orders_id
                                              and orders_status_id = :orders_status_id
                                              and comments like "PayPal App: Capture (%"
                                              ');
                $Qc->bindInt(':orders_id', $order['orders_id']);
                $Qc->bindInt(':orders_status_id', CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID);
                $Qc->execute();

                while ($Qc->fetch()) {
                    if (preg_match('/^PayPal App\: Capture \(([0-9\.]+)\)\n/', $Qc->value('comments'), $c_matches)) {
                    $capture_total -= $this->app->formatCurrencyRaw($c_matches[1], $order['currency'], 1);
                }
            }

            if ($capture_total > 0) {
                $output .= HTML::button($this->app->getDef('button_dialog_void'), 'fas fa-times-circle', '#', ['params' => 'data-button="paypalButtonDoVoid"'], 'warning');

                $dialog_title = HTML::outputProtected($this->app->getDef('dialog_void_title'));
                $dialog_body = $this->app->getDef('dialog_void_body');
                $void_link = CLICSHOPPING::link(null, 'A&Orders\Orders&Edit&oID=' . $order['orders_id'] .'&page=' . $_GET['page'] . '&tabaction=doVoid');
                $dialog_button_void = $this->app->getDef('dialog_void_button_void');
                $dialog_button_cancel = $this->app->getDef('dialog_void_button_cancel');

                $output .= <<<EOD
<div id="paypal-dialog-void" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{$dialog_title}</h4>
      </div>

      <div class="modal-body">
        <p>{$dialog_body}</p>
      </div>

      <div class="modal-footer">
        <button id="paypal-dialog-void-button" type="button" class="btn btn-success">{$dialog_button_void}</button>
        <button type="button" class="btn btn-warning" data-dismiss="modal">{$dialog_button_cancel}</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
  $('a[data-button="paypalButtonDoVoid"]').click(function(e) {
    e.preventDefault();

    $('#paypal-dialog-void').modal('show');
  });

  $('#paypal-dialog-void-button').on('click', function() {
    window.location = '{$void_link}';
  });
});
</script>
EOD;
                }
            }
        }

        return $output;
    }

    protected function getRefundButton($status, $order)
    {
        $output = '';

        $tids = [];

        $Qc = $this->app->db->prepare('select comments
                                      from :table_orders_status_history
                                      where orders_id = :orders_id
                                      and orders_status_id = :orders_status_id
                                      and comments like "PayPal App: %"
                                      order by date_added desc
                                      ');
        $Qc->bindInt(':orders_id', $_GET['oID']);
        $Qc->bindInt(':orders_status_id', CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID);
        $Qc->execute();

        if ($Qc->fetch() !== false) {
            do {
                if (strpos($Qc->value('comments'), 'PayPal App: Refund') !== false) {
                    preg_match('/Parent ID\: ([A-Za-z0-9]+)$/', $Qc->value('comments'), $ppr_matches);

                    $tids[$ppr_matches[1]]['Refund'] = true;
                } elseif (strpos($Qc->value('comments'), 'PayPal App: Capture') !== false) {
                    preg_match('/^PayPal App\: Capture \(([0-9\.]+)\).*Transaction ID\: ([A-Za-z0-9]+)/s', $Qc->value('comments'), $ppr_matches);

                    $tids[$ppr_matches[2]]['Amount'] = $ppr_matches[1];
                }
            } while ($Qc->fetch());
        } elseif ($status['Payment Status'] == 'Completed') {
            $tids[$status['Transaction ID']]['Amount'] = $this->app->formatCurrencyRaw($order['total'], $order['currency'], $order['currency_value']);
        }

        $can_refund = false;

        foreach ($tids as $value) {
            if (!isset($value['Refund'])) {
                $can_refund = true;
                break;
            }
        }

        if ($can_refund === true) {
            $output .= HTML::button($this->app->getDef('button_dialog_refund'), 'fas fa-minus-circle', '#', ['params' => 'data-button="paypalButtonRefundTransaction"'], 'danger');

            $dialog_title = HTML::outputProtected($this->app->getDef('dialog_refund_title'));
            $dialog_body = $this->app->getDef('dialog_refund_body');
            $refund_link = CLICSHOPPING::link(null, 'A&Orders\Orders&Edit&oID=' . $_GET['oID'] . '&page=' . $_GET['page'] . '&tabaction=refundTransaction');
            $dialog_button_refund = $this->app->getDef('dialog_refund_button_refund');
            $dialog_button_cancel = $this->app->getDef('dialog_refund_button_cancel');

            $refund_fields = '';

            $counter = 0;

            foreach ($tids as $key => $value) {
                $refund_fields .= '<div class="checkbox"><label' . (isset($value['Refund']) ? ' style="text-decoration: line-through;"' : '') . '><input type="checkbox" name="ppRefund[]" value="' . $key . '" id="ppRefundPartial' . $counter . '"' . (isset($value['Refund']) ? ' disabled="disabled"' : '') . ' /> ' . $this->app->getDef('dialog_refund_payment_title', [
                    'amount' => $value['Amount']
                ]) . '</label></div>';

                $counter++;
            }

            $output .= <<<EOD
<div id="paypal-dialog-refund" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{$dialog_title}</h4>
      </div>

      <div class="modal-body">
        <form id="ppRefundForm" action="{$refund_link}" method="post">
          <p>{$dialog_body}</p>

          {$refund_fields}
        </form>
      </div>

      <div class="modal-footer">
        <button id="paypal-dialog-refund-button" type="button" class="btn btn-danger">{$dialog_button_refund}</button>
        <button type="button" class="btn btn-warning" data-dismiss="modal">{$dialog_button_cancel}</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
  $('a[data-button="paypalButtonRefundTransaction"]').click(function(e) {
    e.preventDefault();

    $('#paypal-dialog-refund').modal('show');
  });

  $('#paypal-dialog-refund-button').on('click', function() {
    $('#ppRefundForm').submit();
  });
});
</script>
EOD;
        }

        return $output;
    }
}
