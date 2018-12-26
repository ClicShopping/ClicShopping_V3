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

class Action implements \ClicShopping\OM\Modules\HooksInterface
{
    protected $app;
    protected $ms;

    public function __construct()
    {
        if (!Registry::exists('PayPal')) {
            Registry::set('PayPal', new PayPalApp());
        }

        $this->app = Registry::get('PayPal');

        $this->ms = Registry::get('MessageStack');

        $this->app->loadDefinitions('hooks/ClicShoppingAdmin/orders/action');
    }

    public function execute() {
        if (isset($_GET['tabaction'])) {
            $Qstatus = $this->app->db->prepare('select comments
                                                from :table_orders_status_history
                                                where orders_id = :orders_id
                                                and orders_status_id = :orders_status_id
                                                and comments like "%Transaction ID:%"
                                                order by date_added
                                                limit 1
                                                ');
            $Qstatus->bindInt(':orders_id', $_GET['oID']);
            $Qstatus->bindInt(':orders_status_id', CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID);
            $Qstatus->execute();

            if ($Qstatus->fetch() !== false) {
                $pp = [];

                foreach (explode("\n", $Qstatus->value('comments')) as $s) {
                    if (!empty($s) && (strpos($s, ':') !== false)) {
                        $entry = explode(':', $s, 2);

                        $pp[trim($entry[0])] = trim($entry[1]);
                    }
                }

                if (isset($pp['Transaction ID'])) {
                    $Qorder = $this->app->db->prepare('select o.orders_id,
                                                              o.payment_method,
                                                              o.currency,
                                                              o.currency_value,
                                                              ot.value as total
                                                        from :table_orders o,
                                                             :table_orders_total ot
                                                        where o.orders_id = :orders_id
                                                        and o.orders_id = ot.orders_id
                                                        and (ot.class = "ot_total" or ot.class = "TO")
                                                        ');
                    $Qorder->bindInt(':orders_id', $_GET['oID']);
                    $Qorder->execute();

                    switch ($_GET['tabaction']) {
                        case 'getTransactionDetails':
                            $this->getTransactionDetails($pp, $Qorder->toArray());
                            break;

                        case 'doCapture':
                            $this->doCapture($pp, $Qorder->toArray());
                            break;

                        case 'doVoid':
                            $this->doVoid($pp, $Qorder->toArray());
                            break;

                        case 'refundTransaction':
                            $this->refundTransaction($pp, $Qorder->toArray());
                            break;
                    }

                    CLICSHOPPING::redirect(null, 'A&Orders\Orders&Edit&oID=' . $_GET['oID'] . '&page=' . $_GET['page'] . '#section_status_history_content');
                }
            }
        }
    }

    protected function getTransactionDetails($comments, $order)   {
      $CLICSHOPPING_Order = Registry::get('Order');

        $result = null;

        if (!isset($comments['Gateway'])) {
            $response = $this->app->getApiResult('APP', 'GetTransactionDetails', [
                                                                                   'TRANSACTIONID' => $comments['Transaction ID']
                                                                                 ], (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

            if (in_array($response['ACK'], ['Success', 'SuccessWithWarning'])) {
                $result = 'Transaction ID: ' . HTML::sanitize($response['TRANSACTIONID']) . "\n" .
                          'Payer Status: ' . HTML::sanitize($response['PAYERSTATUS']) . "\n" .
                          'Address Status: ' . HTML::sanitize($response['ADDRESSSTATUS']) . "\n" .
                          'Payment Status: ' . HTML::sanitize($response['PAYMENTSTATUS']) . "\n" .
                          'Payment Type: ' . HTML::sanitize($response['PAYMENTTYPE']) . "\n" .
                          'Pending Reason: ' . HTML::sanitize($response['PENDINGREASON']);
           }
        } elseif ($comments['Gateway'] == 'Payflow') {
            $response = $this->app->getApiResult('APP', 'PayflowInquiry', [
                                                                            'ORIGID' => $comments['Transaction ID']
                                                                          ], (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

            if (isset($response['RESULT']) && ($response['RESULT'] == '0')) {
                $result = 'Transaction ID: ' . HTML::sanitize($response['ORIGPNREF']) . "\n" .
                          'Gateway: Payflow' . "\n";

                $pending_reason = $response['TRANSSTATE'];
                $payment_status = null;

                switch ($response['TRANSSTATE']) {
                    case '3':
                        $pending_reason = 'authorization';
                        $payment_status = 'Pending';
                        break;

                    case '4':
                        $pending_reason = 'other';
                        $payment_status = 'In-Progress';
                        break;

                    case '6':
                        $pending_reason = 'scheduled';
                        $payment_status = 'Pending';
                        break;

                    case '8':
                    case '9':
                        $pending_reason = 'None';
                        $payment_status = 'Completed';
                        break;
                }

                if (isset($payment_status)) {
                    $result .= 'Payment Status: ' . HTML::sanitize($payment_status) . "\n";
                }

                $result .= 'Pending Reason: ' . HTML::sanitize($pending_reason) . "\n";

                switch ($response['AVSADDR']) {
                    case 'Y':
                        $result .= 'AVS Address: Match' . "\n";
                        break;

                    case 'N':
                        $result .= 'AVS Address: No Match' . "\n";
                        break;
                }

                switch ($response['AVSZIP']) {
                    case 'Y':
                        $result .= 'AVS ZIP: Match' . "\n";
                        break;

                    case 'N':
                        $result .= 'AVS ZIP: No Match' . "\n";
                        break;
                }

                switch ($response['IAVS']) {
                    case 'Y':
                        $result .= 'IAVS: International' . "\n";
                        break;

                    case 'N':
                        $result .= 'IAVS: USA' . "\n";
                        break;
                }

                switch ($response['CVV2MATCH']) {
                    case 'Y':
                        $result .= 'CVV2: Match' . "\n";
                        break;

                    case 'N':
                        $result .= 'CVV2: No Match' . "\n";
                        break;
                }
            }
        }

        if (!empty($result)) {
            $sql_data_array = [
                              'orders_id' => (int)$order['orders_id'],
                              'orders_status_id' => CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
                              'orders_status_invoice_id' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
                              'admin_user_name' => '',
                              'date_added' => 'now()',
                              'customer_notified' => '0',
                              'comments' => $result
                              ];

            $this->app->db->save('orders_status_history', $sql_data_array);

            $this->ms->add($this->app->getDef('ms_success_getTransactionDetails'), 'success');
        } else {
            $this->ms->add($this->app->getDef('ms_error_getTransactionDetails'), 'error');
        }
    }

    protected function doCapture($comments, $order) {
      $CLICSHOPPING_Order = Registry::get('Order');

        $pass = false;

        $capture_total = $capture_value = $this->app->formatCurrencyRaw($order['total'], $order['currency'], $order['currency_value']);
        $capture_final = true;

        if ($this->app->formatCurrencyRaw($_POST['ppCaptureAmount'], $order['currency'], 1) < $capture_value) {
            $capture_value = $this->app->formatCurrencyRaw($_POST['ppCaptureAmount'], $order['currency'], 1);
            $capture_final = (isset($_POST['ppCatureComplete']) && ($_POST['ppCatureComplete'] == 'true')) ? true : false;
        }

        if (!isset($comments['Gateway'])) {
            $params = [
                      'AUTHORIZATIONID' => $comments['Transaction ID'],
                      'AMT' => $capture_value,
                      'CURRENCYCODE' => $order['currency'],
                      'COMPLETETYPE' => ($capture_final === true) ? 'Complete' : 'NotComplete'
                      ];

            $response = $this->app->getApiResult('APP', 'DoCapture', $params, (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

            if (in_array($response['ACK'], ['Success', 'SuccessWithWarning'])) {
                $transaction_id = $response['TRANSACTIONID'];

                $pass = true;
            }
        } elseif ($comments['Gateway'] == 'Payflow') {
            $params = [
                'ORIGID' => $comments['Transaction ID'],
                'AMT' => $capture_value,
                'CAPTURECOMPLETE' => ($capture_final === true) ? 'Y' : 'N'
            ];

            $response = $this->app->getApiResult('APP', 'PayflowCapture', $params, (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

            if (isset($response['RESULT']) && ($response['RESULT'] == '0')) {
                $transaction_id = $response['PNREF'];

                $pass = true;
            }
        }

        if ($pass === true) {
            $result = 'PayPal App: Capture (' . $capture_value . ')' . "\n";

            if (($capture_value < $capture_total) && ($capture_final === true)) {
                $result .= 'PayPal App: Void (' . $this->app->formatCurrencyRaw($capture_total - $capture_value, $order['currency'], 1) . ')' . "\n";
            }

            $result .= 'Transaction ID: ' . HTML::sanitize($transaction_id);

            $sql_data_array = [
                              'orders_id' => (int)$order['orders_id'],
                              'orders_status_id' => CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
                              'orders_status_invoice_id' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
                              'admin_user_name' => '',
                              'date_added' => 'now()',
                              'customer_notified' => '0',
                              'comments' => $result
                           ];

            $this->app->db->save('orders_status_history', $sql_data_array);

            $this->ms->add($this->app->getDef('ms_success_doCapture'), 'success');
        } else {
            $this->ms->add($this->app->getDef('ms_error_doCapture'), 'error');
        }
    }

    protected function doVoid($comments, $order) {
      $CLICSHOPPING_Order = Registry::get('Order');

        $pass = false;

        if (!isset($comments['Gateway'])) {
            $response = $this->app->getApiResult('APP', 'DoVoid', [
                                                                    'AUTHORIZATIONID' => $comments['Transaction ID']
                                                                  ], (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

            if (in_array($response['ACK'], ['Success', 'SuccessWithWarning'])) {
                $pass = true;
            }
        } elseif ($comments['Gateway'] == 'Payflow') {
            $response = $this->app->getApiResult('APP', 'PayflowVoid', [
                                                                        'ORIGID' => $comments['Transaction ID']
                                                                        ], (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

            if (isset($response['RESULT']) && ($response['RESULT'] == '0')) {
                $pass = true;
            }
        }

        if ($pass === true) {
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

            $result = 'PayPal App: Void (' . $capture_total . ')';

            $sql_data_array = [
                              'orders_id' => (int)$order['orders_id'],
                              'orders_status_id' => CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
                              'orders_status_invoice_id' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
                              'admin_user_name' => '',
                              'date_added' => 'now()',
                              'customer_notified' => '0',
                              'comments' => $result
                              ];

            $this->app->db->save('orders_status_history', $sql_data_array);

            $this->ms->add($this->app->getDef('ms_success_doVoid'), 'success');
        } else {
            $this->ms->add($this->app->getDef('ms_error_doVoid'), 'error');
        }
    }

    protected function refundTransaction($comments, $order)  {
      $CLICSHOPPING_Order = Registry::get('Order');

        if (isset($_POST['ppRefund'])) {
            $tids = [];

            $Qc = $this->app->db->prepare('select comments
                                          from :table_orders_status_history
                                          where orders_id = :orders_id
                                          and orders_status_id = :orders_status_id
                                          and comments
                                          like "PayPal App: %"
                                          order by date_added
                                          desc
                                          ');
            $Qc->bindInt(':orders_id', $order['orders_id']);
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
            } elseif ($comments['Payment Status'] == 'Completed') {
                $tids[$comments['Transaction ID']]['Amount'] = $this->app->formatCurrencyRaw($order['total'], $order['currency'], $order['currency_value']);
            }

            $rids = [];

            foreach ($_POST['ppRefund'] as $id) {
                if (isset($tids[$id]) && !isset($tids[$id]['Refund'])) {
                    $rids[] = $id;
                }
            }

            foreach ($rids as $id) {
                $pass = false;

                if (!isset($comments['Gateway'])) {
                    $response = $this->app->getApiResult('APP', 'RefundTransaction', [
                        'TRANSACTIONID' => $id
                    ], (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

                    if (in_array($response['ACK'], ['Success', 'SuccessWithWarning'])) {
                        $transaction_id = $response['REFUNDTRANSACTIONID'];

                        $pass = true;
                    }
                } elseif ($comments['Gateway'] == 'Payflow') {
                    $response = $this->app->getApiResult('APP', 'PayflowRefund', [
                        'ORIGID' => $id
                    ], (strpos($order['payment_method'], 'Sandbox') === false) ? 'live' : 'sandbox');

                    if (isset($response['RESULT']) && ($response['RESULT'] == '0')) {
                        $transaction_id = $response['PNREF'];

                        $pass = true;
                    }
                }

                if ($pass === true) {
                    $result = 'PayPal App: Refund (' . $tids[$id]['Amount'] . ')' . "\n" .
                              'Transaction ID: ' . HTML::sanitize($transaction_id) . "\n" .
                              'Parent ID: ' . HTML::sanitize($id);

                    $sql_data_array = [
                                        'orders_id' => (int)$order['orders_id'],
                                        'orders_status_invoice_id' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
                                        'admin_user_name' => '',
                                        'date_added' => 'now()',
                                        'customer_notified' => '0',
                                        'comments' => $result
                                      ];

                    $this->app->db->save('orders_status_history', $sql_data_array);

                    $this->ms->add($this->app->getDef('ms_success_refundTransaction', [
                                                                                       'refund_amount' => $tids[$id]['Amount']
                                                                                       ]
                                                      ), 'success');
                } else {
                    $this->ms->add($this->app->getDef('ms_error_refundTransaction', [
                                                                                     'refund_amount' => $tids[$id]['Amount']
                                                                                     ]
                                                      ), 'error');
                }
            }
        }
    }
}
