<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\Stripe\Module\Payment;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Payment\Stripe\Stripe as StripeApp;
use ClicShopping\Sites\Common\B2BCommon;
use Stripe\PaymentIntent;
use Stripe\Stripe as StripeAPI;
/**
 * Class ST
 *
 * This class represents the Stripe Payment integration module for the ClicShopping environment.
 * It implements the PaymentInterface to handle all payment-related processes, including
 * enabling/disabling the module, updating its status, and processing payments.
 *
 * The class interacts with the Stripe API for secure payment handling and supports configurations
 * like custom order statuses, payment zones, and B2B group-based access.
 */

class ST implements \ClicShopping\OM\Modules\PaymentInterface
{
  public string $code;
  public $title;
  public $description;
  public $enabled = false;
  public mixed $app;
  protected $currency;
  public $signature;
  public $public_title;
  public int|null $sort_order = 0;
  protected $api_version;
  public $group;

  public $intent;
  public $private_key;
  public $public_key;

  /**
   * Constructor method for initializing the Stripe payment module.
   *
   * This method sets up the necessary configurations and properties required
   * for the Stripe payment module. It handles registry initialization,
   * module definitions, versioning, API configuration, and enabling or disabling
   * the module based on certain conditions, such as customer group settings
   * and environment (production or test).
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Customer = Registry::get('Customer');

    if (Registry::exists('Order')) {
      $CLICSHOPPING_Order = Registry::get('Order');
    }

    if (!Registry::exists('Stripe')) {
      Registry::set('Stripe', new StripeApp());
    }

    $this->app = Registry::get('Stripe');
    $this->app->loadDefinitions('Module/Shop/ST/ST');

    $this->signature = 'Stripe|' . $this->app->getVersion() . '|1.0';
    $this->api_version = $this->app->getApiVersion();

    $this->code = 'ST';
    $this->title = $this->app->getDef('module_stripe_title');
    $this->public_title = $this->app->getDef('module_stripe_public_title');

// Activation module du paiement selon les groupes B2B
    if (\defined('CLICSHOPPING_APP_STRIPE_ST_STATUS')) {
      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        if (B2BCommon::getPaymentUnallowed($this->code)) {
          if (CLICSHOPPING_APP_STRIPE_ST_STATUS == 'True') {
            $this->enabled = true;
          } else {
            $this->enabled = false;
          }
        }
      } else {
        if (CLICSHOPPING_APP_STRIPE_ST_NO_AUTHORIZE == 'True' && $CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
          if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
            if (CLICSHOPPING_APP_STRIPE_ST_STATUS == 'True') {
              $this->enabled = true;
            } else {
              $this->enabled = false;
            }
          }
        }
      }

      if ((int)CLICSHOPPING_APP_STRIPE_ST_PREPARE_ORDER_STATUS_ID > 0) {
        $this->order_status = CLICSHOPPING_APP_STRIPE_ST_PREPARE_ORDER_STATUS_ID;
      }

      if ($this->enabled === true) {
        if (isset($CLICSHOPPING_Order) && \is_object($CLICSHOPPING_Order)) {
          $this->update_status();
        }
      }

      if (CLICSHOPPING_APP_STRIPE_ST_SERVER_PROD == 'True') {
        $this->private_key = CLICSHOPPING_APP_STRIPE_ST_PRIVATE_KEY;
        $this->public_key = CLICSHOPPING_APP_STRIPE_ST_PUBLIC_KEY;
      } else {
        $this->private_key = CLICSHOPPING_APP_STRIPE_ST_PRIVATE_KEY_TEST;
        $this->public_key = CLICSHOPPING_APP_STRIPE_ST_PUBLIC_KEY_TEST;
      }

      $this->sort_order = \defined('CLICSHOPPING_APP_STRIPE_ST_SORT_ORDER') ? CLICSHOPPING_APP_STRIPE_ST_SORT_ORDER : 0;
    }
  }

  /**
   * Updates the status of the payment module based on the delivery address and configured geo zones.
   *
   * This method checks if the module is enabled and if the delivery address matches the configured geo zone restrictions.
   * If the delivery address does not fall within the specified geo zones, the module is disabled.
   *
   * @return void
   */
  public function update_status()
  {
    $CLICSHOPPING_Order = Registry::get('Order');

    if (($this->enabled === true) && ((int)CLICSHOPPING_APP_STRIPE_ST_ZONE > 0)) {
      $check_flag = false;

      $sql_array = [
        'geo_zone_id' => CLICSHOPPING_APP_STRIPE_ST_ZONE,
        'zone_country_id' => $CLICSHOPPING_Order->delivery['country']['id']
      ];

      $Qcheck = $this->app->db->get('zones_to_geo_zones', 'zone_id', $sql_array, 'zone_id');

      while ($Qcheck->fetch()) {
        if (($Qcheck->valueInt('zone_id') < 1) || ($Qcheck->valueInt('zone_id') === $CLICSHOPPING_Order->delivery['zone_id'])) {
          $check_flag = true;
          break;
        }
      }

      if ($check_flag === false) {
        $this->enabled = false;
      }
    }
  }

  /**
   * Provides JavaScript validation for the application.
   *
   * @return bool Returns false indicating that JavaScript validation is not implemented or disabled.
   */
  public function javascript_validation()
  {
    return false;
  }

  /**
   * Prepares and returns the module selection details.
   *
   * This method builds the module's selection details, including the module's
   * unique identifier and public title. If a Stripe logo is set and available
   * in the specified directory, it appends the logo to the public title.
   *
   * @return array An associative array containing the 'id' as the unique
   * module identifier and 'module' as the public title, optionally including
   * a logo image if configured.
   */
  public function selection()
  {
    $CLICSHOPPING_Template = Registry::get('Template');

    if (!empty(CLICSHOPPING_APP_STRIPE_ST_LOGO)) {
      if (!empty(CLICSHOPPING_APP_STRIPE_ST_LOGO) && is_file($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/payment/' . CLICSHOPPING_APP_STRIPE_ST_LOGO)) {
        $this->public_title = $this->public_title . '&nbsp;&nbsp;&nbsp;' . HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/payment/' . CLICSHOPPING_APP_STRIPE_ST_LOGO);
      } else {
        $this->public_title = $this->public_title;
      }
    }

    return [
      'id' => $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code,
      'module' => $this->public_title
    ];
  }

  /**
   * Performs a pre-confirmation check before processing the payment.
   *
   * @return bool Always returns false.
   */
  public function pre_confirmation_check()
  {
//      $CLICSHOPPING_Template = Registry::get(('Template'));
//      $result = $CLICSHOPPING_Template->addBlock($this->getSubmitCardDetailsJavascript(), 'footer_scripts');
    return false;
  }

  /**
   * Creates a payment intent and prepares the necessary information for confirming payment with Stripe.
   *
   * This method initializes the Stripe payment intent using order and customer details.
   * It generates metadata based on the order's products and details, sets up either automatic
   * or manual transaction capture mode depending on configuration, and includes relevant payment
   * information in the confirmation content. The content includes embedded JavaScript for Stripe
   * payment handling and essential input data.
   *
   * @return array The confirmation details, including generated HTML content and payment intent data,
   *         to be displayed for confirming payment with Stripe.
   */
  public function confirmation()
  {
    $CLICSHOPPING_Order = Registry::get('Order');
    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_Address = Registry::get('Address');

    StripeAPI::setApiKey($this->private_key);

    $customer_id = $CLICSHOPPING_Customer->getId();
    $currency = mb_strtoupper($CLICSHOPPING_Order->info['currency']);
    $total_amount = $CLICSHOPPING_Order->info['total'] * 100;
    $total_amount = str_replace('.', '', $total_amount);  // Chargeable amount

    $metadata = ['customer_id' => (int)$customer_id,
      'customer_name' => $CLICSHOPPING_Customer->getName(),
      'customer_laster_name' => $CLICSHOPPING_Customer->getLastName(),
      'company' => HTML::output($CLICSHOPPING_Order->customer['company'])
    ];

    $i = 0;

    if (\count($CLICSHOPPING_Order->products) < 7) {
      foreach ($CLICSHOPPING_Order->products as $product) {
        $i++;

        $metadata['product_' . $i . '_name'] = $product['name'];
        $metadata['product_' . $i . '_model'] = $product['model'];
        $metadata['product_' . $i . '_id'] = $product['id'];
        $metadata['product_' . $i . '_qty'] = $product['qty'];
        $metadata['product_' . $i . '_price'] = $product['price'];
        $metadata['product_' . $i . '_tax'] = $product['tax'];
      }
    }

    if (CLICSHOPPING_APP_STRIPE_ST_TRANSACTION_METHOD == 'automatic') {
      $capture_method = 'automatic';
    } else {
      $capture_method = 'manual';
    }

// have to create intent before loading the javascript because it needs the intent id
    if (isset($_SESSION['stripe_payment_intent_id'])) {
      $stripe_payment_intent_id = HTML::sanitize($_SESSION['stripe_payment_intent_id']);

      try {
        $this->intent = PaymentIntent::retrieve(retrieve(['id' => $stripe_payment_intent_id]));
        // $this->event_log($customer_id, 'page retrieve intent', $stripe_payment_intent_id, $this->intent);
        $this->intent->amount = $total_amount; //$CLICSHOPPING_Order->info['total'],
        $this->intent->currency = $currency;
        $this->intent->metadata = $metadata;

        $this->intent->save();
//          $response = $this->intent->save();

      } catch (exception $err) {
        //$this->event_log($customer_id, 'page create intent', $stripe_payment_intent_id, $err->getMessage());
// failed to save existing intent, so create new one
        unset($stripe_payment_intent_id);
      }
    }

    if (!isset($stripe_payment_intent_id)) {
      $description = STORE_NAME . ' - Order date time : ' . date('Y-m-d H:i:s');
      $token = $_POST['stripeToken'] ?? [];

      $params = [
        'amount' => $total_amount,
        'currency' => $currency,
        'source' => $token,
        'setup_future_usage' => 'off_session',
        'description' => $description,
        'capture_method' => $capture_method,
        'metadata' => $metadata,
        'payment_method_types' => ['card'],
      ];

      $this->intent = PaymentIntent::create($params);

      // $this->event_log($customer_id, "page create intent", json_encode($params), $this->intent);
      $stripe_payment_intent_id = $this->intent->id;

      unset($_SESSION['stripe_payment_intent_id']);
    }

    $content = '';

    if (CLICSHOPPING_APP_STRIPE_ST_SERVER_PROD == 'False') {
      $content .= '<div class="alert alert-warning"> ' . $this->app->getDef('text_stripe_alert_mode_test') . '</div>';
    }

    $content .= $this->app->getDef('text_stripe_title');
    $content .= '<div class="mt-1"></div>';
// have to create intent before loading the javascript because it needs the intent id
    $content .= '<input type="hidden" id="intent_id" value="' . HTML::output($stripe_payment_intent_id) . '" />' .
      '<input type="hidden" id="secret" value="' . HTML::output($this->intent->client_secret) . '" />';
    $content .= '<div id="stripe_table_new_card">' .
      '<div><label for="cardholder-name" class="control-label">' . $this->app->getDef('text_stripe_credit_card_owner') . '</label>' .
      '<div><input type="text" id="cardholder-name" class="form-control" value="' . HTML::output($CLICSHOPPING_Order->billing['firstname'] . ' ' . $CLICSHOPPING_Order->billing['lastname']) . '" required></text></div>
                  </div>' .
      '<div class="mt-1"></div>' .
      '<div><label for="card-element" class="control-label">' . $this->app->getDef('text_stripe_credit_card_type') . '</label>' .
      '<div id="card-element" class="col-md-5"></div>
                  </div>';

    /*
          if (MODULE_PAYMENT_STRIPE_SCA_TOKENS == 'True') {
            $content .= '<div class="form-check">' .
                '<div>' . tep_draw_checkbox_field('card-save', 'true', null, 'class="form-check-input') . '<label class="form-check-label">' . MODULE_PAYMENT_STRIPE_SCA_CREDITCARD_SAVE . '</label></div></div>';
          }
    */

    $content .= '<div id="card-errors" role="alert" class="messageStackError payment-errors"></div></div>';
    $content .= '<input type="hidden" id="city" value="' . $CLICSHOPPING_Order->billing['city'] . '" />';
    $content .= '<input type="hidden" id="line1" value="' . HTML::output($CLICSHOPPING_Order->customer['street_address']) . '" />';
    $content .= '<input type="hidden" id="line2" value="' . HTML::output($CLICSHOPPING_Order->billing['suburb']) . '" />';
    $content .= '<input type="hidden" id="postal_code" value="' . HTML::output($CLICSHOPPING_Order->customer['postcode']) . '" />';
    $content .= '<input type="hidden" id="state" value="' . $CLICSHOPPING_Address->getZoneName($CLICSHOPPING_Order->billing['country']['id'], $CLICSHOPPING_Order->billing['zone_id'], $CLICSHOPPING_Order->billing['state']) . '" />';
    $content .= '<input type="hidden" id="country" value="' . $CLICSHOPPING_Order->billing['country']['iso_code_2'] . '" />';
    $content .= '<input type="hidden" id="email_address" value="' . HTML::output($CLICSHOPPING_Order->customer['email_address']) . '" />';
    $content .= '<input type="hidden" id="customer_id" value="' . HTML::output($customer_id) . '" />';

    $content .= $this->getSubmitCardDetailsJavascript();

    $confirmation = ['title' => $content];

    return $confirmation;
  }

  /**
   * Processes the button action for the payment or form submission.
   *
   * @return bool Returns false to indicate no further processing is required.
   */
  public function process_button()
  {
    return false;
  }

  /**
   * Executes any necessary logic prior to processing an operation.
   *
   * @return bool Returns false indicating no processing is performed.
   */
  public function before_process()
  {
    return false;
  }

  /**
   * Processes additional operations after a successful order placement.
   *
   * This method retrieves the last order ID and updates its status and history
   * in the database. If the order ID is unavailable, it retrieves the latest
   * order ID from the database. It determines the new order status using the
   * configured Stripe order status ID or falls back to the default order status ID.
   * The function also logs any relevant comments with the updated order status.
   *
   * @return void
   */
  public function after_process()
  {
    $CLICSHOPPING_Order = Registry::get('Order');

    $orders_id = $CLICSHOPPING_Order->getLastOrderId();

    if (empty($orders_id) || $orders_id == 0 || \is_null($orders_id)) {
      $Qorder = $CLICSHOPPING_Order->db->prepare('select orders_id
                                                    from :table_orders                                                    
                                                    order by orders_id desc
                                                    limit 1
                                                   ');
      $Qorder->execute();

      $orders_id = $Qorder->valueInt('orders_id');
    }

    $comment = $this->app->getDef('text_reference_transaction');

    if (CLICSHOPPING_APP_STRIPE_ST_ORDER_STATUS_ID == 0 || empty(CLICSHOPPING_APP_STRIPE_ST_ORDER_STATUS_ID)) {
      $new_order_status = DEFAULT_ORDERS_STATUS_ID;
    } else {
      $new_order_status = CLICSHOPPING_APP_STRIPE_ST_ORDER_STATUS_ID;
    }

    $sql_data_array = [
      'orders_id' => $orders_id,
      'orders_status_id' => (int)$new_order_status,
      'date_added' => 'now()',
      'customer_notified' => '0',
      'comments' => $comment
    ];

    $this->app->db->save('orders_status_history', $sql_data_array);

    $sql_data_array = ['orders_status' => (int)$new_order_status];
    $sql_insert = ['orders_id' => (int)$orders_id];

    $this->app->db->save('orders', $sql_data_array, $sql_insert);
  }

  /**
   * Retrieves the error details based on the provided error parameter in the URL.
   *
   * This method checks for the presence of an 'error' parameter in the URL. If found and its value matches specific cases,
   * it updates the error message accordingly. Otherwise, it uses a general error message. The result is an associative
   * array containing the error's title and message.
   *
   * @return array An associative array with two keys:
   *               - 'title': The title of the error message.
   *               - 'error': The detailed error message.
   */
  public function get_error()
  {
    $message = $this->app->getDef('module_stripe_error_general');

    if (isset($_GET['error']) && !empty($_GET['error'])) {
      switch ($_GET['error']) {
        case 'cardstored':
          $message = $this->app->getDef('module_stripe_error_stripe');
          break;
      }
    }

    $error = ['title' => $this->app->getDef('module_stripe_error_title'),
      'error' => $message
    ];

    return $error;
  }

  /**
   * Checks if the Stripe module status is defined and not an empty string.
   *
   * @return bool True if the Stripe module status is set and not empty, false otherwise.
   */
  public function check()
  {
    return \defined('CLICSHOPPING_APP_STRIPE_ST_STATUS') && (trim(CLICSHOPPING_APP_STRIPE_ST_STATUS) != '');
  }

  /**
   * Redirects to the installation configuration page for the specified module.
   *
   * @return void
   */
  public function install()
  {
    $this->app->redirect('Configure&Install&module=Stripe');
  }

  /**
   * Redirects to the uninstall module configuration page for Stripe.
   *
   * @return void
   */
  public function remove()
  {
    $this->app->redirect('Configure&Uninstall&module=Stripe');
  }

  /**
   * Retrieves the configuration keys used by the Stripe module.
   *
   * @return array An array of configuration key names.
   */
  public function keys()
  {
    return array('CLICSHOPPING_APP_STRIPE_ST_SORT_ORDER');
  }


  /**
   * Generates the JavaScript necessary for handling Stripe payment processing,
   * including creating and managing card elements, submitting the payment form,
   * and handling saved cards or new card details for Stripe intents.
   *
   * @param mixed $intent Optional argument for passing additional intent-related information.
   *                      Defaults to null if not provided.
   * @return string The JavaScript code as a string to be included on the payment processing page.
   */
  public function getSubmitCardDetailsJavascript($intent = null)
  {
    $stripe_publishable_key = $this->public_key;

//        $intent_url = tep_href_link("ext/modules/payment/stripe_sca/payment_intent.php", '', 'SSL', false, false);
    $intent_url = ''; // return url

    $js = <<<EOD
<style>
#stripe_table_new_card #card-element {
  background-color: #fff;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
<script src="https://js.stripe.com/v3/"></script>
<script>
$(function() {
    $('[name=checkout_confirmation]').attr('id','payment-form');

    var stripe = Stripe('{$stripe_publishable_key}');
    var elements = stripe.elements();

    // Create an instance of the card Element.
    var card = elements.create('card', {hidePostalCode: true});
    
    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

    $('#payment-form').submit(function(event) {
        var \$form = $(this);

        // Disable the submit button to prevent repeated clicks
        \$form.find('button').prop('disabled', true);

        var selected =  $("input[name='stripe_card']:checked"). val();
        var cc_save = $('[name=card-save]').prop('checked');
        try {
            if ((selected != null && selected != '0') || cc_save) {
                // update intent to use saved card, then process payment if successful
                updatePaymentIntent(cc_save,selected);
            } else {
                // using new card details without save
                processNewCardPayment();
            }
        } catch ( error ) {
            \$form.find('.payment-errors').text(error);
        }

        // Prevent the form from submitting with the default action
        return false;
    });

    if ( $('#stripe_table').length > 0 ) {
        if ( typeof($('#stripe_table').parent().closest('table').attr('width')) == 'undefined' ) {
          $('#stripe_table').parent().closest('table').attr('width', '100%');
        }

        $('#stripe_table .moduleRowExtra').hide();

        $('#stripe_table_new_card').hide();
        $('#card-element').prop('id','new-card-element');
        $('#save-card-element').prop('id','card-element');

        $('form[name="checkout_confirmation"] input[name="stripe_card"]').change(function() {

            if ( $(this).val() == '0' ) {
                stripeShowNewCardFields();
            } else {
                if ($('#stripe_table_new_card').is(':visible')) {
                    $('#card-element').prop('id','new-card-element');
                    $('#save-card-element').prop('id','card-element');
                }
                $('#stripe_table_new_card').hide();

            }
            $('tr[id^="stripe_card_"]').removeClass('moduleRowSelected');
            $('#stripe_card_' + $(this).val()).addClass('moduleRowSelected');
            });

        $('form[name="checkout_confirmation"] input[name="stripe_card"]:first').prop('checked', true).trigger('change');

        $('#stripe_table .moduleRow').hover(function() {
            $(this).addClass('moduleRowOver');
        }, function() {
            $(this).removeClass('moduleRowOver');
        }).click(function(event) {
            var target = $(event.target);

            if ( !target.is('input:radio')) {
                $(this).find('input:radio').each(function() {
                    if ( $(this).prop('checked') == false ) {
                        $(this).prop('checked', true).trigger('change');
                    }
                });
            }
            });
    } else {
        if ( typeof($('#stripe_table_new_card').parent().closest('table').attr('width')) == 'undefined' ) {
            $('#stripe_table_new_card').parent().closest('table').attr('width', '100%');
        }
    }
    function stripeShowNewCardFields() {
        $('#card-element').attr('id','save-card-element');
        $('#new-card-element').attr('id','card-element');

        $('#stripe_table_new_card').show();
    }
    function updatePaymentIntent(cc_save,token){
        // add card save option to payment intent, so card can be saved in webhook
        // or customer/payment method if using saved card
        $.getJSON( "{$intent_url}",{"id":$('#intent_id').val(),
                                    "token":token, 
                                    "customer_id": $('#customer_id').val(), 
                                    "cc_save": cc_save},
        function( data ) {
            if (data.status == 'ok') {
                var selected = $("input[name='stripe_card']:checked"). val();

                if (selected == null || selected == '0') {
                    processNewCardPayment();
                } else {
                    processSavedCardPayment(data.payment_method);
                }
            } else {
                var \$form = $('#payment-form');
                \$form.find('button').prop('disabled', false);
                $('#card-errors').text(data.error);    
            }
        });
    }
    function processNewCardPayment() {
        stripe.handleCardPayment(
            $('#secret').val(), card, {
              payment_method_data: {
                billing_details: {
                    name: $('#cardholder-name').val(),
                    address: {
                        city: $('#city').val(),
                        line1: $('#line1').val(),
                        postal_code: $('#postal_code').val(),
                        state: $('#state').val(),
                        country: $('#country').val()
                    },
                    email: $('#email_address').val()
                }
              }
            }
        ).then(function(result) { 
            stripeResponseHandler(result);
        });
    }
    function processSavedCardPayment(payment_method_id) {
        stripe.handleCardPayment(
            $('#secret').val(), 
            {
              payment_method: payment_method_id
            }
        ).then(function(result) { 
            stripeResponseHandler(result);
        });
    }
    function stripeResponseHandler(result) {
        var \$form = $('#payment-form');
        if (result.error) {
            $('#card-errors').text(result.error.message);
            \$form.find('button').prop('disabled', false);
        } else {
            $('#card-errors').text('Processing');

            // Insert the token into the form so it gets submitted to the server
            \$form.append($('<input type="hidden" name="stripeIntentId" />').val(result.paymentIntent.id));
            // and submit
            \$form.get(0).submit();
        }
    }
});
</script>
EOD;

    return $js;
  }
}