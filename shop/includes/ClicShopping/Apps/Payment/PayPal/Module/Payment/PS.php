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

  namespace ClicShopping\Apps\Payment\PayPal\Module\Payment;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Payment\PayPal\PayPal as PayPalApp;

  use ClicShopping\Sites\Shop\Tax;

  use ClicShopping\Sites\Common\B2BCommon;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop\TemplateEmail;

  class PS implements \ClicShopping\OM\Modules\PaymentInterface
  {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public $app;

    protected $lang;

    public function __construct()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (Registry::exists('Order')) {
        $CLICSHOPPING_Order = Registry::get('Order');
      }

      $this->lang = Registry::get('Language');

      if (!Registry::exists('PayPal')) {
        Registry::set('PayPal', new PayPalApp());
      }

      $this->app = Registry::get('PayPal');
      $this->app->loadDefinitions('modules/PS/PS');

      $this->signature = 'paypal|paypal_standard|' . $this->app->getVersion() . '|2.4';
      $this->api_version = $this->app->getApiVersion();

      $this->code = 'PS';
      $this->title = $this->app->getDef('module_ps_title');
      $this->public_title = $this->app->getDef('module_ps_public_title');
      $this->description = '<div class="text-md-center">' . HTML::button($this->app->getDef('module_ps_legacy_admin_app_button'), null, $this->app->link('Configure&module=PS'), 'primary') . '</div>';

// Activation module du paiement selon les groupes B2B

      if (defined('CLICSHOPPING_APP_PAYPAL_PS_STATUS')) {
        if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
          if (B2BCommon::getPaymentUnallowed($this->code)) {

            if (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '2' || CLICSHOPPING_APP_PAYPAL_PS_STATUS == '1') {
              $this->enabled = true;
            } else {
              $this->enabled = false;
            }
          }
        } else {
          if (defined('CLICSHOPPING_APP_PAYPAL_PS_NO_AUTHORIZE') && CLICSHOPPING_APP_PAYPAL_PS_NO_AUTHORIZE == 'True' && $CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
            if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {

              if (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '2' || CLICSHOPPING_APP_PAYPAL_PS_STATUS == '1') {
                $this->enabled = true;
              } else {
                $this->enabled = false;
              }
            }
          }
        }
      }

      $this->sort_order = defined('CLICSHOPPING_APP_PAYPAL_PS_SORT_ORDER') ? CLICSHOPPING_APP_PAYPAL_PS_SORT_ORDER : 0;

      $this->order_status = defined('CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID') && ((int)CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID > 0) ? (int)CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID : 0;

      if (defined('CLICSHOPPING_APP_PAYPAL_PS_STATUS')) {
        if (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '2') {
          $this->title .= ' [Sandbox]';
          $this->public_title .= ' (' . $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code . '; Sandbox)';
        }

        if (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '1') {
          $this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';
        } else {
          $this->form_action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }
      }

      if (!function_exists('curl_init')) {
        $this->description .= '<div class="alert alert-warning" role="alert">' . $this->app->getDef('module_ps_error_curl') . '</div>';

        $this->enabled = false;
      }

      if ($this->enabled === true) {
        if (!$this->app->hasCredentials('PS', 'email')) {
          $this->description .= '<div class="alert alert-warning" role="alert">' . $this->app->getDef('module_ps_error_credentials') . '</div>';

          $this->enabled = false;
        }
      }

      if ($this->enabled === true) {
        if (isset($CLICSHOPPING_Order) && is_object($CLICSHOPPING_Order)) {
          $this->update_status();
        }
      }

// Before the stock quantity check is performed in Checkout&Process, detect if the quantity
// has already beed deducated in the IPN to avoid a quantity == 0 redirect
      if ($this->enabled === true) {
        if (isset($_GET['Checkout']) && isset($_GET['Process'])) {
          if (isset($_SESSION['payment']) && ($_SESSION['payment'] == $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code)) {
            $this->pre_before_check();
          }
        }
      }
    }

    public function update_status()
    {
      $CLICSHOPPING_Order = Registry::get('Order');

      if (($this->enabled === true) && ((int)CLICSHOPPING_APP_PAYPAL_PS_ZONE > 0)) {
        $check_flag = false;

        $Qcheck = $this->app->db->get('zones_to_geo_zones', 'zone_id', ['geo_zone_id' => CLICSHOPPING_APP_PAYPAL_PS_ZONE,
          'zone_country_id' => $CLICSHOPPING_Order->billing['country']['id']
        ],
          'zone_id'
        );

        while ($Qcheck->fetch()) {
          if (($Qcheck->valueInt('zone_id') < 1) || ($Qcheck->valueInt('zone_id') == $CLICSHOPPING_Order->billing['zone_id'])) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag === false) {
          $this->enabled = false;
        }
      }
    }

    public function javascript_validation()
    {
      return false;
    }

    public function selection()
    {

      if (isset($_SESSION['cart_PayPal_Standard_ID'])) {
        $this->order_id = substr($_SESSION['cart_PayPal_Standard_ID'], strpos($_SESSION['cart_PayPal_Standard_ID'], '-') + 1);

        $Qcheck = $this->app->db->get('orders_status_history', 'orders_id', ['orders_id' => $this->order_id], null, 1);

        if ($Qcheck->fetch() === false) {
          $this->app->db->delete('orders', ['orders_id' => $this->order_id]);
          $this->app->db->delete('orders_total', ['orders_id' => $this->order_id]);
          $this->app->db->delete('orders_products', ['orders_id' => $this->order_id]);
          $this->app->db->delete('orders_products_attributes', ['orders_id' => $this->order_id]);
          $this->app->db->delete('orders_products_download', ['orders_id' => $this->order_id]);

          unset($_SESSION['cart_PayPal_Standard_ID']);
        }
      }

      if (CLICSHOPPING_APP_PAYPAL_PS_LOGO == 'True') {
        $this->public_title = $this->public_title . '&nbsp;&nbsp;&nbsp;<img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" border="0" alt="PayPal Logo" style="padding: 3px;" />';
      }

      return array('id' => $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, 'module' => $this->public_title);
    }

    public function pre_confirmation_check()
    {
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Order = Registry::get('Order');

      if (empty($CLICSHOPPING_ShoppingCart->cartID)) {
        $_SESSION['cartID'] = $CLICSHOPPING_ShoppingCart->cartID = $CLICSHOPPING_ShoppingCart->generate_cart_id();
      }

      $CLICSHOPPING_Order->info['payment_method_raw'] = $CLICSHOPPING_Order->info['payment_method'];
      $CLICSHOPPING_Order->info['payment_method'] = '<img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" border="0" alt="PayPal Logo" style="padding: 3px;" />';
    }

    public function confirmation()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Prod = Registry::get('Prod');
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_OrderTotal = Registry::get('OrderTotal');
      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

      if (isset($_SESSION['cartID'])) {
        $insert_order = false;

        if (isset($_SESSION['cart_PayPal_Standard_ID'])) {
          $this->order_id = substr($_SESSION['cart_PayPal_Standard_ID'], strpos($_SESSION['cart_PayPal_Standard_ID'], '-') + 1);

          $Qorder = $this->app->db->get('orders', 'currency', ['orders_id' => (int)$this->order_id]);

          if (($Qorder->value('currency') != $CLICSHOPPING_Order->info['currency']) || ($_SESSION['cartID'] != substr($_SESSION['cart_PayPal_Standard_ID'], 0, strlen($_SESSION['cartID'])))) {

            $Qcheck = $this->app->db->get('orders_status_history', 'orders_id', ['orders_id' => (int)$this->order_id],
              null,
              1
            );

            if ($Qcheck->fetch() === false) {
              $this->app->db->delete('orders', ['orders_id' => $this->order_id]);
              $this->app->db->delete('orders_total', ['orders_id' => $this->order_id]);
              $this->app->db->delete('orders_products', ['orders_id' => $this->order_id]);
              $this->app->db->delete('orders_products_attributes', ['orders_id' => $this->order_id]);
              $this->app->db->delete('orders_products_download', ['orders_id' => $this->order_id]);
            }

            $insert_order = true;
          }
        } else {
          $insert_order = true;
        }

        if ($insert_order === true) {
          $order_totals = [];

          if (is_array($CLICSHOPPING_OrderTotal->modules)) {
            $order_total = $CLICSHOPPING_OrderTotal->process();

            foreach ($order_total as $value) {
              if (!is_null($value['title']) && !is_null($value['title'])) {
                $order_totals[] = ['code' => $value['code'],
                  'title' => $value['title'],
                  'text' => $value['text'],
                  'value' => $value['value'],
                  'sort_order' => $value['sort_order']
                ];
              }
            }
          }

//gdpr
          $Qgdpr = $this->app->db->prepare('select no_ip_address
                                            from :table_customers_gdpr
                                            where customers_id = :customers_id
                                           ');
          $Qgdpr->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qgdpr->execute();

          if ($Qgdpr->valueInt('no_ip_address') == 1) {
            $client_computer_ip = '';
            $provider_name_client = '';
          } else {
            $client_computer_ip = HTTP::getIPAddress();
            $provider_name_client = HTTP::getProviderNameCustomer();
          }

          if (isset($CLICSHOPPING_Order->info['payment_method_raw'])) {
            $CLICSHOPPING_Order->info['payment_method'] = $CLICSHOPPING_Order->info['payment_method_raw'];
            unset($CLICSHOPPING_Order->info['payment_method_raw']);
          }

          $sql_data_array = ['customers_id' => (int)$CLICSHOPPING_Customer->getID(),
            'customers_group_id' => (int)$CLICSHOPPING_Order->customer['customers_group_id'],
            'customers_name' => $CLICSHOPPING_Order->customer['firstname'] . ' ' . $CLICSHOPPING_Order->customer['lastname'],
            'customers_company' => $CLICSHOPPING_Order->customer['company'],
            'customers_street_address' => $CLICSHOPPING_Order->customer['street_address'],
            'customers_suburb' => $CLICSHOPPING_Order->customer['suburb'],
            'customers_city' => $CLICSHOPPING_Order->customer['city'],
            'customers_postcode' => $CLICSHOPPING_Order->customer['postcode'],
            'customers_state' => $CLICSHOPPING_Order->customer['state'],
            'customers_country' => $CLICSHOPPING_Order->customer['country']['title'],
            'customers_telephone' => $CLICSHOPPING_Order->customer['telephone'],
            'customers_email_address' => $CLICSHOPPING_Order->customer['email_address'],
            'customers_address_format_id' => (int)$CLICSHOPPING_Order->customer['format_id'],
            'delivery_name' => trim($CLICSHOPPING_Order->delivery['firstname'] . ' ' . $CLICSHOPPING_Order->delivery['lastname']),
            'delivery_company' => $CLICSHOPPING_Order->delivery['company'],
            'delivery_street_address' => $CLICSHOPPING_Order->delivery['street_address'],
            'delivery_suburb' => $CLICSHOPPING_Order->delivery['suburb'],
            'delivery_city' => $CLICSHOPPING_Order->delivery['city'],
            'delivery_postcode' => $CLICSHOPPING_Order->delivery['postcode'],
            'delivery_state' => $CLICSHOPPING_Order->delivery['state'],
            'delivery_country' => $CLICSHOPPING_Order->delivery['country']['title'],
            'delivery_address_format_id' => (int)$CLICSHOPPING_Order->delivery['format_id'],
            'billing_name' => $CLICSHOPPING_Order->billing['firstname'] . ' ' . $CLICSHOPPING_Order->billing['lastname'],
            'billing_company' => $CLICSHOPPING_Order->billing['company'],
            'billing_street_address' => $CLICSHOPPING_Order->billing['street_address'],
            'billing_suburb' => $CLICSHOPPING_Order->billing['suburb'],
            'billing_city' => $CLICSHOPPING_Order->billing['city'],
            'billing_postcode' => $CLICSHOPPING_Order->billing['postcode'],
            'billing_state' => $CLICSHOPPING_Order->billing['state'],
            'billing_country' => $CLICSHOPPING_Order->billing['country']['title'],
            'billing_address_format_id' => (int)$CLICSHOPPING_Order->billing['format_id'],
            'payment_method' => $CLICSHOPPING_Order->info['payment_method'],
            'cc_type' => $CLICSHOPPING_Order->info['cc_type'],
            'cc_owner' => $CLICSHOPPING_Order->info['cc_owner'],
            'cc_number' => $CLICSHOPPING_Order->info['cc_number'],
            'cc_expires' => $CLICSHOPPING_Order->info['cc_expires'],
            'date_purchased' => 'now()',
            'orders_status' => (int)$this->order_status,
            'orders_status_invoice' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
            'currency' => $CLICSHOPPING_Order->info['currency'],
            'currency_value' => $CLICSHOPPING_Order->info['currency_value'],
            'client_computer_ip' => $client_computer_ip,
            'provider_name_client' => $provider_name_client,
            'customers_cellular_phone' => $CLICSHOPPING_Order->customer['cellular_phone']
          ];

// recuperation des informations societes pour les clients B2B (voir fichier la classe OrderAdmin)
          if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
            $sql_data_array['customers_siret'] = $CLICSHOPPING_Order->customer['siret'];
            $sql_data_array['customers_ape'] = $CLICSHOPPING_Order->customer['ape'];
            $sql_data_array['customers_tva_intracom'] = $CLICSHOPPING_Order->customer['tva_intracom'];
          }

          $this->app->db->save('orders', $sql_data_array);

          $insert_id = $this->app->db->lastInsertId();

          $page_manager_general_condition = $CLICSHOPPING_PageManagerShop->pageManagerGeneralCondition();

          $sql_data_array = ['orders_id' => (int)$insert_id,
            'customers_id' => (int)$CLICSHOPPING_Customer->getID(),
            'page_manager_general_condition' => $page_manager_general_condition
          ];

          $this->app->db->save('orders_pages_manager', $sql_data_array);

// orders total
          for ($i = 0, $n = count($order_totals); $i < $n; $i++) {
            $sql_data_array = ['orders_id' => (int)$insert_id,
              'title' => $order_totals[$i]['title'],
              'text' => $order_totals[$i]['text'],
              'value' => (float)$order_totals[$i]['value'],
              'class' => $order_totals[$i]['code'],
              'sort_order' => (int)$order_totals[$i]['sort_order']
            ];

            $this->app->db->save('orders_total', $sql_data_array);
          }

          for ($i = 0, $n = count($CLICSHOPPING_Order->products); $i < $n; $i++) {
// search the good model
            if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
              $QproductsModuleCustomersGroup = $this->app->db->prepare('select products_model_group
                                                                         from :table_products_groups
                                                                         where products_id = :products_id
                                                                         and customers_group_id =  :customers_group_id
                                                                        ');
              $QproductsModuleCustomersGroup->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));
              $QproductsModuleCustomersGroup->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
              $QproductsModuleCustomersGroup->execute();

              $products_model = $QproductsModuleCustomersGroup->value('products_model_group');

              if (empty($products_model)) $products_model = $CLICSHOPPING_Order->products[$i]['model'];
            } else {
              $products_model = $CLICSHOPPING_Order->products[$i]['model'];
            }

// save data
            $sql_data_array = ['orders_id' => (int)$insert_id,
              'products_id' => (int)$CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']),
              'products_model' => $products_model,
              'products_name' => $CLICSHOPPING_Order->products[$i]['name'],
              'products_price' => (float)$CLICSHOPPING_Order->products[$i]['price'],
              'final_price' => (float)$CLICSHOPPING_Order->products[$i]['final_price'],
              'products_tax' => (float)$CLICSHOPPING_Order->products[$i]['tax'],
              'products_quantity' => (int)$CLICSHOPPING_Order->products[$i]['qty']
            ];

            $this->app->db->save('orders_products', $sql_data_array);

            $order_products_id = $this->app->db->lastInsertId();

            if (isset($CLICSHOPPING_Order->products[$i]['attributes'])) {
              for ($j = 0, $n2 = count($CLICSHOPPING_Order->products[$i]['attributes']); $j < $n2; $j++) {

                $Qattributes = $CLICSHOPPING_ProductsAttributes->getAttributesDownloaded($CLICSHOPPING_Order->products[$i]['id'], $CLICSHOPPING_Order->products[$i]['attributes'][$j]['option_id'], $CLICSHOPPING_Order->products[$i]['attributes'][$j]['value_id'], $this->app->lang->getId());

                $sql_data_array = ['orders_id' => (int)$insert_id,
                  'orders_products_id' => (int)$order_products_id,
                  'products_options' => $Qattributes->value('products_options_name'),
                  'products_options_values' => $Qattributes->value('products_options_values_name'),
                  'options_values_price' => (float)$Qattributes->value('options_values_price'),
                  'price_prefix' => $Qattributes->value('price_prefix'),
                  'products_attributes_reference' => $Qattributes->value('products_attributes_reference')
                ];

                $this->app->db->save('orders_products_attributes', $sql_data_array);

                if ((DOWNLOAD_ENABLED == 'true') && $Qattributes->hasValue('products_attributes_filename') && !empty($Qattributes->value('products_attributes_filename'))) {
                  $sql_data_array = ['orders_id' => (int)$insert_id,
                    'orders_products_id' => (int)$order_products_id,
                    'orders_products_filename' => $Qattributes->value('products_attributes_filename'),
                    'download_maxdays' => (int)$Qattributes->value('products_attributes_maxdays'),
                    'download_count' => (int)$Qattributes->value('products_attributes_maxcount')
                  ];

                  $this->app->db->save('orders_products_download', $sql_data_array);
                }
              }
            }
          }

          $_SESSION['cart_PayPal_Standard_ID'] = $_SESSION['cartID'] . '-' . $insert_id;
        }
      }

      return false;
    }


    /***********************************************************
     * process_button
     ***********************************************************/
    public function process_button()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_OrderTotal = Registry::get('OrderTotal');
      $CLICSHOPPING_Address = Registry::get('Address');

      $total_tax = $CLICSHOPPING_Order->info['tax'];

// remove shipping tax in total tax value
      if (isset($_SESSION['shipping']['cost'])) {
        $total_tax -= ($CLICSHOPPING_Order->info['shipping_cost'] - $_SESSION['shipping']['cost']);
      }

      $process_button_string = '';

      $parameters = ['cmd' => '_cart',
        'upload' => '1',
        'item_name_1' => STORE_NAME,
        'shipping_1' => $this->app->formatCurrencyRaw($CLICSHOPPING_Order->info['shipping_cost']),
        'business' => $this->app->getCredentials('PS', 'email'),
        'amount_1' => $this->app->formatCurrencyRaw($CLICSHOPPING_Order->info['total'] - $CLICSHOPPING_Order->info['shipping_cost'] - $total_tax),
        'currency_code' => $_SESSION['currency'],
        'invoice' => substr($_SESSION['cart_PayPal_Standard_ID'], strpos($_SESSION['cart_PayPal_Standard_ID'], '-') + 1),
        'custom' => $CLICSHOPPING_Customer->getID(),
        'no_note' => '1',
        'notify_url' => CLICSHOPPING::link(null, 'order&ipn&paypal&ps&language=' . $this->lang->get('code'), false, false),
        'rm' => '2',
        'return' => CLICSHOPPING::link(null, 'Checkout&Process'),
        'cancel_return' => CLICSHOPPING::link(null, 'Checkout&Billing'),
        'bn' => $this->app->getIdentifier(),
        'paymentaction' => (CLICSHOPPING_APP_PAYPAL_PS_TRANSACTION_METHOD == '1') ? 'sale' : 'authorization'
      ];

      $return_link_title = $this->app->getDef('module_ps_button_return_to_store', ['store_name' => STORE_NAME]);

      if (strlen($return_link_title) <= 60) {
        $parameters['cbt'] = $return_link_title;
      }


//discount coupons

//pb format_raw
      /*
            if(is_array($CLICSHOPPING_Order->coupon->applied_discount) && ($discount_amount = array_sum($CLICSHOPPING_Order->coupon->applied_discount)) > 0) {
              $parameters['discount_amount_cart'] = $this->format_raw($discount_amount);
            }
      */
      if (is_numeric($_SESSION['sendto']) && ($_SESSION['sendto'] > 0)) {
        $parameters['address_override'] = '1';
        $parameters['first_name'] = $CLICSHOPPING_Order->delivery['firstname'];
        $parameters['last_name'] = $CLICSHOPPING_Order->delivery['lastname'];
        $parameters['address1'] = $CLICSHOPPING_Order->delivery['street_address'];
        $parameters['address2'] = $CLICSHOPPING_Order->delivery['suburb'];
        $parameters['city'] = $CLICSHOPPING_Order->delivery['city'];
        $parameters['state'] = $CLICSHOPPING_Address->getZoneCode($CLICSHOPPING_Order->delivery['country']['id'], $CLICSHOPPING_Order->delivery['zone_id'], $CLICSHOPPING_Order->delivery['state']);
        $parameters['zip'] = $CLICSHOPPING_Order->delivery['postcode'];
        $parameters['country'] = $CLICSHOPPING_Order->delivery['country']['iso_code_2'];
      } else {
        $parameters['no_shipping'] = '1';
        $parameters['first_name'] = $CLICSHOPPING_Order->billing['firstname'];
        $parameters['last_name'] = $CLICSHOPPING_Order->billing['lastname'];
        $parameters['address1'] = $CLICSHOPPING_Order->billing['street_address'];
        $parameters['address2'] = $CLICSHOPPING_Order->billing['suburb'];
        $parameters['city'] = $CLICSHOPPING_Order->billing['city'];
        $parameters['state'] = $CLICSHOPPING_Address->getZoneCode($CLICSHOPPING_Order->billing['country']['id'], $CLICSHOPPING_Order->billing['zone_id'], $CLICSHOPPING_Order->billing['state']);
        $parameters['zip'] = $CLICSHOPPING_Order->billing['postcode'];
        $parameters['country'] = $CLICSHOPPING_Order->billing['country']['iso_code_2'];
      }

      if (!is_null(CLICSHOPPING_APP_PAYPAL_PS_PAGE_STYLE)) {
        $parameters['page_style'] = CLICSHOPPING_APP_PAYPAL_PS_PAGE_STYLE;
      }

      $item_params = [];

      $line_item_no = 1;

      foreach ($CLICSHOPPING_Order->products as $product) {
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $product_price = $this->app->formatCurrencyRaw($product['final_price'] + Tax::calculate($product['final_price'], $product['tax']));
        } else {
          $product_price = $this->app->formatCurrencyRaw($product['final_price']);
        }

        $item_params['item_name_' . $line_item_no] = $product['name'];
        $item_params['amount_' . $line_item_no] = $product_price;
        $item_params['quantity_' . $line_item_no] = $product['qty'];

        $line_item_no++;
      }

      $items_total = $this->app->formatCurrencyRaw($CLICSHOPPING_Order->info['subtotal']);

      $has_negative_price = false;

// order totals are processed on checkout confirmation but not captured into a variable
      if (is_array($CLICSHOPPING_OrderTotal->modules)) {
        $order_totals = $CLICSHOPPING_OrderTotal->process();

        foreach ($order_totals as $value) {
          if (!is_null($value['title']) && !is_null($value['title'])) {
            if (!in_array($value['code'], array('ot_subtotal', 'ot_shipping', 'ot_tax', 'ot_total', 'ST', 'SH', 'TX'))) {
              $item_params['item_name_' . $line_item_no] = $value['title'];
              $item_params['amount_' . $line_item_no] = $this->app->formatCurrencyRaw($value['value']);

              $items_total += $item_params['amount_' . $line_item_no];

              if ($item_params['amount_' . $line_item_no] < 0) {
                $has_negative_price = true;
              }

              $line_item_no++;
            }
          }
        }
      }

      $paypal_item_total = $items_total + $parameters['shipping_1'];

      if (DISPLAY_PRICE_WITH_TAX == 'false') {
        $item_params['tax_cart'] = $this->app->formatCurrencyRaw($total_tax);

        $paypal_item_total += $item_params['tax_cart'];
      }

      if (($has_negative_price === false) && ($this->app->formatCurrencyRaw($paypal_item_total) == $this->app->formatCurrencyRaw($CLICSHOPPING_Order->info['total']))) {
        $parameters = array_merge($parameters, $item_params);
      } else {
        $parameters['tax_cart'] = $this->app->formatCurrencyRaw($total_tax);
      }

      if (CLICSHOPPING_APP_PAYPAL_PS_EWP_STATUS == '1') {
        $parameters['cert_id'] = CLICSHOPPING_APP_PAYPAL_PS_EWP_PUBLIC_CERT_ID;

        $random_string = rand(100000, 999999) . '-' . $CLICSHOPPING_Customer->getID() . '-';

        $data = '';
        foreach ($parameters as $key => $value) {
          $data .= $key . '=' . $value . "\n";
        }

        $fp = fopen(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt', 'w');
        fwrite($fp, $data);
        fclose($fp);

        unset($data);

        if (function_exists('openssl_pkcs7_sign') && function_exists('openssl_pkcs7_encrypt')) {
          openssl_pkcs7_sign(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt', CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', file_get_contents(CLICSHOPPING_APP_PAYPAL_PS_EWP_PUBLIC_CERT), file_get_contents(CLICSHOPPING_APP_PAYPAL_PS_EWP_PRIVATE_KEY), array('From' => $this->app->getCredentials('PS', 'email')), PKCS7_BINARY);

          unlink(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt');

// remove headers from the signature
          $signed = file_get_contents(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
          $signed = explode("\n\n", $signed);
          $signed = base64_decode($signed[1]);

          $fp = fopen(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', 'w');
          fwrite($fp, $signed);
          fclose($fp);

          unset($signed);

          openssl_pkcs7_encrypt(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt', file_get_contents(CLICSHOPPING_APP_PAYPAL_PS_EWP_PAYPAL_CERT), array('From' => $this->app->getCredentials('PS', 'email')), PKCS7_BINARY);

          unlink(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');

// remove headers from the encrypted result
          $data = file_get_contents(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
          $data = explode("\n\n", $data);
          $data = '-----BEGIN PKCS7-----' . "\n" . $data[1] . "\n" . '-----END PKCS7-----';

          unlink(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
        } else {
          exec(CLICSHOPPING_APP_PAYPAL_PS_EWP_OPENSSL . ' smime -sign -in ' . CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt -signer ' . CLICSHOPPING_APP_PAYPAL_PS_EWP_PUBLIC_CERT . ' -inkey ' . CLICSHOPPING_APP_PAYPAL_PS_EWP_PRIVATE_KEY . ' -outform der -nodetach -binary > ' . CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
          unlink(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt');

          exec(CLICSHOPPING_APP_PAYPAL_PS_EWP_OPENSSL . ' smime -encrypt -des3 -binary -outform pem ' . CLICSHOPPING_APP_PAYPAL_PS_EWP_PAYPAL_CERT . ' < ' . CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt > ' . CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
          unlink(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');

          $fh = fopen(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt', 'rb');
          $data = fread($fh, filesize(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt'));
          fclose($fh);

          unlink(CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
        }

        $process_button_string = HTML::hiddenField('cmd', '_s-xclick') .
          HTML::hiddenField('encrypted', $data);

        unset($data);
      } else {
        foreach ($parameters as $key => $value) {
          $process_button_string .= HTML::hiddenField($key, $value);
        }
      }

      $process_button_string .= '<div class="text-md-right">' . HTML::button($this->app->getDef('text_button_paypal'), null, null, 'primary',  ['type' => 'submit', 'params' => 'onclick="submitButtonClick(event)" data-button="payNow"']) . '</div>';

      return $process_button_string;
    }

    public function pre_before_check()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Order = Registry::get('Order');

      $result = false;

      $pptx_params = [];

      $seller_accounts = array($this->app->getCredentials('PS', 'email'));

      if (!is_null($this->app->getCredentials('PS', 'email_primary'))) {
        $seller_accounts[] = $this->app->getCredentials('PS', 'email_primary');
      }

      if ((isset($_POST['receiver_email']) && in_array($_POST['receiver_email'], $seller_accounts)) || (isset($_POST['business']) && in_array($_POST['business'], $seller_accounts)) || (isset($_POST['receiver_id']) && in_array($_POST['receiver_id'], $seller_accounts))) {
        $parameters = 'cmd=_notify-validate&';

        foreach ($_POST as $key => $value) {
          if ($key != 'cmd') {
            $parameters .= $key . '=' . urlencode(stripslashes($value)) . '&';
          }
        }

        $parameters = substr($parameters, 0, -1);

        $result = $this->app->makeApiCall($this->form_action_url, $parameters);

        $pptx_params = $_POST;
        $pptx_params['cmd'] = '_notify-validate';

        foreach ($_GET as $key => $value) {
          $pptx_params['GET ' . $key] = $value;
        }

        $this->app->log('PS', $pptx_params['cmd'], ($result == 'VERIFIED') ? 1 : -1, $pptx_params, $result, (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox');

      } elseif (isset($_GET['tx'])) { // PDT
        if (!is_null(CLICSHOPPING_APP_PAYPAL_PS_PDT_IDENTITY_TOKEN)) {
          $pptx_params['cmd'] = '_notify-synch';

          $parameters = 'cmd=_notify-synch&tx=' . urlencode($_GET['tx']) . '&at=' . urlencode(CLICSHOPPING_APP_PAYPAL_PS_PDT_IDENTITY_TOKEN);

          $pdt_raw = $this->app->makeApiCall($this->form_action_url, $parameters);

          if (!empty($pdt_raw)) {
            $pdt = explode("\n", trim($pdt_raw));

            if (isset($pdt[0])) {
              if ($pdt[0] == 'SUCCESS') {
                $result = 'VERIFIED';

                unset($pdt[0]);
              } else {
                $result = $pdt_raw;
              }
            }

            if (is_array($pdt) && !empty($pdt)) {
              foreach ($pdt as $line) {
                $p = explode('=', $line, 2);

                if (count($p) === 2) {
                  $pptx_params[trim($p[0])] = trim(urldecode($p[1]));
                }
              }
            }
          }

          foreach ($_GET as $key => $value) {
            $pptx_params['GET ' . $key] = $value;
          }

          $this->app->log('PS', $pptx_params['cmd'], ($result == 'VERIFIED') ? 1 : -1, $pptx_params, $result, (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox');
        } else {
          $details = $this->app->getApiResult('PS', 'GetTransactionDetails', array('TRANSACTIONID' => $_GET['tx']), (CLICSHOPPING_APP_PAYPAL_DP_STATUS == '1') ? 'live' : 'sandbox');

          if (in_array($details['ACK'], array('Success', 'SuccessWithWarning'))) {
            $result = 'VERIFIED';

            $pptx_params = ['txn_id' => $details['TRANSACTIONID'],
              'invoice' => $details['INVNUM'],
              'custom' => $details['CUSTOM'],
              'payment_status' => $details['PAYMENTSTATUS'],
              'payer_status' => $details['PAYERSTATUS'],
              'mc_gross' => $details['AMT'],
              'mc_currency' => $details['CURRENCYCODE'],
              'pending_reason' => $details['PENDINGREASON'],
              'reason_code' => $details['REASONCODE'],
              'address_status' => $details['ADDRESSSTATUS'],
              'payment_type' => $details['PAYMENTTYPE']
            ];
          }
        }
      } else {
        $pptx_params = $_POST;

        $pptx_params['cmd'] = '_notify-validate';

        foreach ($_GET as $key => $value) {
          $pptx_params['GET ' . $key] = $value;
        }

        $this->app->log('PS', $pptx_params['cmd'], ($result == 'VERIFIED') ? 1 : -1, $pptx_params, $result, (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox');
      }

      if ($result != 'VERIFIED') {
        $CLICSHOPPING_MessageStack->add('header', $this->app->getDef('module_ps_error_invalid_transaction'));

        CLICSHOPPING::redirect(null, 'Cart');
      }

      $this->verifyTransaction($pptx_params);

      $this->order_id = substr($_SESSION['cart_PayPal_Standard_ID'], strpos($_SESSION['cart_PayPal_Standard_ID'], '-') + 1);

      $Qorder = $this->app->db->get('orders', 'orders_status', ['orders_id' => (int)$this->order_id,
          'customers_id' => (int)$CLICSHOPPING_Customer->getID()
        ]
      );

      if (($Qorder->fetch() === false) || ($this->order_id != $pptx_params['invoice']) || ($CLICSHOPPING_Customer->getID() != $pptx_params['custom'])) {
        CLICSHOPPING::redirect(null, 'Cart');
      }


// skip before_process() if order was already processed in IPN
      if ($Qorder->valueInt('orders_status') != CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID) {
        if (isset($_SESSION['comments']) && !empty($_SESSION['comments'])) {
          $sql_data_array = ['orders_id' => (int)$this->order_id,
            'orders_status_id' => (int)$Qorder->valueInt('orders_status'),
            'orders_status_invoice_id' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
            'admin_user_name' => '',
            'date_added' => 'now()',
            'customer_notified' => '0',
            'comments' => HTML::sanitize($_SESSION['comments'])
          ];
          $this->app->db->save('orders_status_history', $sql_data_array);
        }

// load the after_process function from the payment modules
        $this->after_process();
      }
    }

    /***********************************************************
     * before_process
     ***********************************************************/

    public function before_process()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Prod = Registry::get('Prod');
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_Address = Registry::get('Address');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');
      $CLICSHOPPING_Template = Registry::get('Template');

      $new_order_status = DEFAULT_ORDERS_STATUS_ID;

      if (CLICSHOPPING_APP_PAYPAL_PS_ORDER_STATUS_ID > 0) {
        $new_order_status = CLICSHOPPING_APP_PAYPAL_PS_ORDER_STATUS_ID;
      }

      $this->app->db->save('orders', ['orders_status' => (int)$new_order_status,
        'last_modified' => 'now()',
      ],
        ['orders_id' => (int)$this->order_id]
      );

      $sql_data_array = ['orders_id' => (int)$this->order_id,
        'orders_status_id' => (int)$new_order_status,
        'orders_status_invoice_id' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
        'admin_user_name' => '',
        'date_added' => 'now()',
        'customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0',
        'comments' => $CLICSHOPPING_Order->info['comments']
      ];

      $this->app->db->save('orders_status_history', $sql_data_array);


//kgt - discount coupons
      if (isset($_SESSION['coupon']) && $CLICSHOPPING_Order->info['coupon'] != '') {
        $sql_data_array = ['coupons_id' => $CLICSHOPPING_Order->info['coupon'],
          'orders_id' => (int)$this->order_id
        ];
        $this->app->db->save('discount_coupons_to_orders', $sql_data_array);
      }

// initialized for the email confirmation
      $products_ordered = '';

      for ($i = 0, $n = count($CLICSHOPPING_Order->products); $i < $n; $i++) {
        if (STOCK_LIMITED == 'true') {
          if (DOWNLOAD_ENABLED == 'true') {
            $stock_query_sql = 'select p.products_quantity,
                                       pad.products_attributes_filename
                                from :table_products p
                                left join :table_products_attributes pa  on p.products_id = pa.products_id
                                left join :table_products_attributes_download pad on pa.products_attributes_id = pad.products_attributes_id
                                where p.products_id = :products_id';

// Will work with only one option for downloadable products
// otherwise, we have to build the query dynamically with a loop
            $products_attributes = (isset($CLICSHOPPING_Order->products[$i]['attributes'])) ? $CLICSHOPPING_Order->products[$i]['attributes'] : '';

            if (is_array($products_attributes)) {
              $stock_query_sql .= ' and pa.options_id = :options_id
                                   and pa.options_values_id = :options_values_id
                                ';
            }

            $Qstock = $this->app->db->prepare($stock_query_sql);
            $Qstock->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));

            if (is_array($products_attributes)) {
              $Qstock->bindInt(':options_id', $products_attributes[0]['option_id']);
              $Qstock->bindInt(':options_values_id', $products_attributes[0]['value_id']);
            }

            $Qstock->execute();
          } else {
            $Qstock = $this->app->db->prepare('select products_quantity,
                                                      products_quantity_alert
                                                from :table_products
                                                where products_id = :products_id
                                              ');

            $Qstock->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));
            $Qstock->execute();
          }

          if ($Qstock->fetch() !== false) {
// do not decrement quantities if products_attributes_filename exists
            if ((DOWNLOAD_ENABLED != 'true') || !is_null($Qstock->value('products_attributes_filename'))) {
// select the good qty in B2B ti decrease the stock. See shopping_cart top display out stock or not
              if ($CLICSHOPPING_Customer->getCustomersGroupID() != '0') {

                $QproductsQuantityCustomersGroup = $this->app->db->prepare('select products_quantity_fixed_group
                                                                            from :table_products_groups
                                                                            where products_id = :products_id
                                                                            and customers_group_id =  :customers_group_id
                                                                           ');
                $QproductsQuantityCustomersGroup->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));
                $QproductsQuantityCustomersGroup->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
                $QproductsQuantityCustomersGroup->execute();

                $products_quantity_customers_group = $QproductsQuantityCustomersGroup->fetch();

// do the exact qty in public function the customer group and product
                $products_quantity_customers_group[$i] = $products_quantity_customers_group['products_quantity_fixed_group'];
              } else {
                $products_quantity_customers_group[$i] = 1;
              }
              $stock_left = $Qstock->valueInt('products_quantity') - ($CLICSHOPPING_Order->products[$i]['qty'] * $products_quantity_customers_group[$i]);
            } else {
              $stock_left = $Qstock->valueInt('products_quantity');
              $stock_products_quantity_alert = $Qstock->valueInt('products_quantity_alert');
            }

// alert an email if the product stock is < stock reorder level
// Alert by mail if a product is 0 or < 0

            if (STOCK_ALERT_PRODUCT_REORDER_LEVEL == 'true') {
              if ((STOCK_ALLOW_CHECKOUT == 'false') && (STOCK_CHECK == 'true')) {
                $warning_stock = STOCK_REORDER_LEVEL;
                $current_stock = $stock_left;

// alert email if stock product alert < warning stock
                if (($stock_products_quantity_alert <= $warning_stock) && ($stock_products_quantity_alert != '0')) {
                  $email_text_subject_stock = stripslashes(CLICSHOPPING::getDef('module_payment_paypal_standard_text_subject_alert_stock'));
                  $email_text_subject_stock = html_entity_decode($email_text_subject_stock);

                  $reorder_stock_email = stripslashes(CLICSHOPPING::getDef('module_payment_paypal_standard_text_reorder_level_text_alert_stock'));
                  $reorder_stock_email = html_entity_decode($reorder_stock_email);
                  $reorder_stock_email .= "\n" . CLICSHOPPING::getDef('module_payment_paypal_standard_text_date_alert') . ' ' . strftime(CLICSHOPPING::getDef('date_format_long')) . "\n" . CLICSHOPPING::getDef('email_text_model') . ' ' . $CLICSHOPPING_Order->products[$i]['model'] . "\n" . CLICSHOPPING::getDef('email_text_products_name') . $CLICSHOPPING_Order->products[$i]['name'] . "\n" . CLICSHOPPING::getDef('email_text_products_id') . ' ' . $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']) . "\n" . '<strong>' . CLICSHOPPING::getDef('email_text_products_url') . '</strong>' . HTTP::getShopUrlDomain() . 'index.php?Products&Description&products_id=' . $CLICSHOPPING_Order->products[$i]['id'] . "\n" . '<strong>' . CLICSHOPPING::getDef('email_text_products_alert_stock') . ' ' . $stock_products_quantity_alert . '</strong>';

                  $CLICSHOPPING_Mail->clicMail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_text_subject_stock, $reorder_stock_email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
                }

                if ($current_stock <= $warning_stock) {
                  $email_text_subject_stock = stripslashes(CLICSHOPPING::getDef('email_text_subject_stock', ['store_name' => STORE_NAME]));
                  $email_text_subject_stock = html_entity_decode($email_text_subject_stock);

                  $reorder_stock_email = stripslashes(CLICSHOPPING::getDef('module_payment_paypal_standard_text_reorder_level_text_alert_stock'));
                  $reorder_stock_email = html_entity_decode($reorder_stock_email);
                  $reorder_stock_email .= "\n" . CLICSHOPPING::getDef('module_payment_paypal_standard_text_date_alert') . ' ' . strftime(CLICSHOPPING::getDef('date_format_long')) . "\n" . CLICSHOPPING::getDef('email_text_model') . ' ' . $CLICSHOPPING_Order->products[$i]['model'] . "\n" . CLICSHOPPING::getDef('email_text_products_name') . $CLICSHOPPING_Order->products[$i]['name'] . "\n" . CLICSHOPPING::getDef('email_text_products_id') . ' ' . $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']) . "\n" . '<strong>' . CLICSHOPPING::getDef('email_text_products_url') . '</strong>' . HTTP::getShopUrlDomain() . 'index.php?Products&Description&products_id=' . $CLICSHOPPING_Order->products[$i]['id'] . "\n" . '<strong>' . CLICSHOPPING::getDef('email_text_products_alert_stock') . ' ' . $current_stock . '</strong>';

                  $CLICSHOPPING_Mail->clicMail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_text_subject_stock, $reorder_stock_email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
                }
              }
            }

            $this->app->db->save('products', ['products_quantity' => (int)$stock_left],
              ['products_id' => (int)$CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id'])]
            );

            if ($stock_left != $Qstock->valueInt('products_quantity')) {
              $this->app->db->save('products', ['products_quantity' => $stock_left], ['products_id' => $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id'])]);
            }

            if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
              $this->app->db->save('products', ['products_status' => '0'],
                ['products_id' => (int)$CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id'])]
              );
            }

// Alert by mail product exhausted if a product is 0 or < 0
            if (STOCK_ALERT_PRODUCT_EXHAUSTED == 'true') {
              if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false') && (STOCK_CHECK == 'true')) {
                $email_text_subject_stock = stripslashes(CLICSHOPPING::getDef('email_text_subject_stock', ['store_name' => STORE_NAME]));
                $email_text_subject_stock = html_entity_decode($email_text_subject_stock);
                $email_product_exhausted_stock = stripslashes(CLICSHOPPING::getDef('email_text_stock_exuasted'));
                $email_product_exhausted_stock = html_entity_decode($email_product_exhausted_stock);
                $email_product_exhausted_stock .= "\n" . CLICSHOPPING::getDef('module_payment_paypal_standard_text_date_alert') . ' ' . strftime(CLICSHOPPING::getDef('date_format_long')) . "\n" . CLICSHOPPING::getDef('email_text_model') . ' ' . $CLICSHOPPING_Order->products[$i]['model'] . "\n" . CLICSHOPPING::getDef('email_text_products_name') . $CLICSHOPPING_Order->products[$i]['name'] . "\n" . CLICSHOPPING::getDef('email_text_products_id') . ' ' . $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']) . "\n";

                $CLICSHOPPING_Mail->clicMail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_text_subject_stock, $email_product_exhausted_stock, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
              }
            } // end stock alert
          }
        }

// Update products_ordered (for bestsellers list)
        $Qupdate = $this->app->db->prepare('update :table_products
                                            set products_ordered = products_ordered + :products_ordered
                                            where products_id = :products_id
                                          ');

        $Qupdate->bindInt(':products_ordered', $CLICSHOPPING_Order->products[$i]['qty']);
        $Qupdate->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));
        $Qupdate->execute();

        $products_ordered_attributes = '';

        if (isset($CLICSHOPPING_Order->products[$i]['attributes'])) {
          for ($j = 0, $n2 = count($CLICSHOPPING_Order->products[$i]['attributes']); $j < $n2; $j++) {

            $Qattributes = $CLICSHOPPING_ProductsAttributes->getAttributesDownloaded($CLICSHOPPING_Order->products[$i]['id'], $CLICSHOPPING_Order->products[$i]['attributes'][$j]['option_id'], $CLICSHOPPING_Order->products[$i]['attributes'][$j]['value_id'], $this->app->lang->getId());

            $products_ordered_attributes .= "\n\t" . $Qattributes->value('products_options_name') . ' ' . $Qattributes->value('products_options_values_name');
          }
        }

//------insert customer choosen option eof ----
        $products_ordered .= $CLICSHOPPING_Order->products[$i]['qty'] . ' x ' . $CLICSHOPPING_Order->products[$i]['name'] . ' (' . $CLICSHOPPING_Order->products[$i]['model'] . ') = ' . $CLICSHOPPING_Currencies->displayPrice($CLICSHOPPING_Order->products[$i]['final_price'], $CLICSHOPPING_Order->products[$i]['tax'], $CLICSHOPPING_Order->products[$i]['qty']) . $products_ordered_attributes . "\n";
      }

      // ---------------------------------------------
      // ------          EMAIL SENT  --------------
      // ---------------------------------------------

// lets start with the email confirmation
      $email_order = STORE_NAME . "\n\n" .
        CLICSHOPPING::getDef('email_separator') . "\n" .
        CLICSHOPPING::getDef('email_text_order_number', ['store_name' => STORE_NAME]) . ' ' . $this->order_id . "\n" .
        CLICSHOPPING::getDef('email_text_invoice_url') . ' ' . CLICSHOPPING::link(null, 'Account&HistoryInfo&order_id=' . (int)$this->order_id) . "\n" .
        CLICSHOPPING::getDef('email_text_date_ordered') . ' ' . strftime(CLICSHOPPING::getDef('date_format_long')) . "\n\n";

      if ($CLICSHOPPING_Order->info['comments']) {
        $email_order .= HTML::output($CLICSHOPPING_Order->info['comments']) . "\n\n";
      }

      $email_order .= CLICSHOPPING::getDef('email_text_products') . "\n";

      $email_order .= CLICSHOPPING::getDef('email_separator') . "\n";
      $email_order .= html_entity_decode($products_ordered) . "\n";
      $email_order .= CLICSHOPPING::getDef('email_separator') . "\n";

      $email_total = '';

      for ($i = 0, $n = count($CLICSHOPPING_Order->totals); $i < $n; $i++) {
        $email_total .= strip_tags($CLICSHOPPING_Order->totals[$i]['title']) . ' ' . strip_tags($CLICSHOPPING_Order->totals[$i]['text']) . "\n";
      }

      $email_order .= $email_total;


      if ($CLICSHOPPING_Order->content_type != 'virtual') {
        $email_order .= "\n" . CLICSHOPPING::getDef('email_text_delivery_address') . "\n" .

          CLICSHOPPING::getDef('email_separator') . "\n" .
          $CLICSHOPPING_Address->addressFormat($CLICSHOPPING_Order->delivery['format_id'], $CLICSHOPPING_Order->delivery, false, '', "\n") . "\n";
      }

      $email_order .= "\n" . CLICSHOPPING::getDef('email_text_billing_address') . "\n" .
        CLICSHOPPING::getDef('email_separator') . "\n" .
        $CLICSHOPPING_Address->addressFormat($CLICSHOPPING_Order->billing['format_id'], $CLICSHOPPING_Order->billing, false, '', "\n") . "\n\n";

      $email_order .= CLICSHOPPING::getDef('email_text_payment_method') . "\n" .
        CLICSHOPPING::getDef('email_separator') . "\n";

      if (isset($_SESSION['payment'])) {
        if (strpos($_SESSION['payment'], '\\') !== false) {
          $code = 'Payment_' . str_replace('\\', '_', $_SESSION['payment']);

          if (Registry::exists($code)) {
            $CLICSHOPPING_PM = Registry::get($code);
          }
        }

        if (isset($CLICSHOPPING_PM)) {
          CLICSHOPPING::getDef('email_separator') . "\n";
          $payment_class = $CLICSHOPPING_PM;

          $email_order .= $payment_class->title . "\n\n";

          if (property_exists(get_class($payment_class), 'email_footer')) {
            $email_order .= $payment_class->email_footer . "\n";
          }
        }
      }

      $email_order .= TemplateEmail::getTemplateEmailSignature() . "\n\n";
      $email_order .= TemplateEmail::getTemplateEmailTextFooter() . "\n\n";

      $CLICSHOPPING_Mail->clicMail($CLICSHOPPING_Order->customer['lastname'], $CLICSHOPPING_Order->customer['email_address'], CLICSHOPPING::getDef('email_text_subject', ['store_name' => STORE_NAME]), $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

// send emails to other people
// SEND_EXTRA_ORDER_EMAILS_TO does'nt work like this, test<test@test.com>, just with test@test.com
      if (!empty(SEND_EXTRA_ORDER_EMAILS_TO)) {
        $email_text_subject = stripslashes(CLICSHOPPING::getDef('email_text_subject', ['store_name' => STORE_NAME]));
        $email_text_subject = html_entity_decode($email_text_subject);
        $text[] = TemplateEmail::getExtractEmailAddress(SEND_EXTRA_ORDER_EMAILS_TO);

        foreach ($text as $key => $email) {
          $CLICSHOPPING_Mail->clicMail('', $email[$key], $email_text_subject, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
      }

// load the after_process public function from the payment modules
      $this->after_process();

      $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/Shop/CheckoutProcess/';

      if (is_dir($source_folder)) {
        $files_get = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'CheckoutProcess*');

        if (is_array($files_get)) {
          foreach ($files_get as $value) {
            if (!empty($value['name'])) {
              $CLICSHOPPING_Hooks->call('CheckoutProcess', $value['name']);
            }
          }
        }
      }
    }

    public function after_process()
    {
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Template = Registry::get('Template');

      $CLICSHOPPING_ShoppingCart->reset(true);

      $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/Shop/CheckoutProcess/';

      if (is_dir($source_folder)) {
        $files_get = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'CheckoutProcess*');

        if (is_array($files_get)) {
          foreach ($files_get as $value) {
            if (!empty($value['name'])) {
              $CLICSHOPPING_Hooks->call('CheckoutProcess', $value['name']);
            }
          }
        }
      }

// unregister session variables used during checkout
      unset($_SESSION['sendto']);
      unset($_SESSION['billto']);
      unset($_SESSION['shipping']);
      unset($_SESSION['payment']);
      unset($_SESSION['comments']);
      unset($_SESSION['coupon']);
      unset($_SESSION['order']);
      unset($_SESSION['order_total']);
      unset($_SESSION['ClicShoppingCart']);
      unset($_SESSION['free_shipping']);

      unset($_SESSION['cart_PayPal_Standard_ID']);

      CLICSHOPPING::redirect(null, 'Checkout&Success');
    }

    public function get_error()
    {
      return false;
    }

    public function check()
    {
      return defined('CLICSHOPPING_APP_PAYPAL_PS_STATUS') && (trim(CLICSHOPPING_APP_PAYPAL_PS_STATUS) != '');
    }

    public function install()
    {
      $this->app->redirect('Configure&Install&module=PS');
    }

    public function remove()
    {
      $this->app->redirect('Configure&Uninstall&module=PS');
    }

    public function keys()
    {
      return array('CLICSHOPPING_APP_PAYPAL_PS_SORT_ORDER');
    }

    public function verifyTransaction($pptx_params, $is_ipn = false)
    {
      $CLICSHOPPING_Order = Registry::get('Order');

      if (isset($pptx_params['invoice']) && is_numeric($pptx_params['invoice']) && ($pptx_params['invoice'] > 0) && isset($pptx_params['custom']) && is_numeric($pptx_params['custom']) && ($pptx_params['custom'] > 0)) {

        $Qorder = $this->app->db->get('orders', ['orders_id', 'currency', 'currency_value'], ['orders_id' => $pptx_params['invoice'], 'customers_id' => $pptx_params['custom']]);

        if ($Qorder->fetch() !== false) {

          $Qtotal = $this->app->db->prepare('select value
                                             from :table_orders_total
                                             where orders_id = :orders_id
                                             and (class = :class or class = :class1)
                                             limit 1
                                            ');
          $Qtotal->bindInt(':orders_id', $Qorder->valueInt('orders_id'));
          $Qtotal->bindValue(':class', 'ot_total');
          $Qtotal->bindValue(':class1', 'TO');
          $Qtotal->execute();

          $comment_status = 'Transaction ID: ' . HTML::outputProtected($pptx_params['txn_id']) . "\n" .
            'Payer Status: ' . HTML::outputProtected($pptx_params['payer_status']) . "\n" .
            'Payment Status: ' . HTML::outputProtected($pptx_params['payment_status']) . "\n" .
            'Payment Type: ' . HTML::outputProtected($pptx_params['payment_type']);

          if (!empty($pptx_params['payment_date'])) {
            $comment_status .=  "\n"  . 'Payment_date: ' . HTML::outputProtected($pptx_params['payment_date']) . "\n";
          }

          if (!empty($pptx_params['invoice'])) {
            $comment_status .= 'Invoice: ' . HTML::outputProtected($pptx_params['invoice']) . "\n";
          }

          if (!empty($pptx_params['address_status'])) {
            $comment_status .= 'Address Status: ' . HTML::outputProtected($pptx_params['address_status']) . "\n";
          }

          if (!empty($pptx_params['pending_reason'])) {
            $comment_status .= 'Pending Reason: ' . HTML::outputProtected($pptx_params['pending_reason']);
          }


          if ($pptx_params['mc_gross'] != $this->app->formatCurrencyRaw($Qtotal->value('value'), $Qorder->value('currency'), $Qorder->value('currency_value'))) {
            $comment_status .= "\n" . 'CLICSHOPPING Error Total Mismatch: PayPal transaction value (' . HTML::outputProtected($pptx_params['mc_gross']) . ') does not match order value (' . $this->app->formatCurrencyRaw($Qtotal->value('value'), $Qorder->value('currency'), $Qorder->value('currency_value')) . ')';
          }

          if ($is_ipn === true) {
            $comment_status .= "\n" . 'Source: IPN';
          }

          $sql_data_array = ['orders_id' => $Qorder->valueInt('orders_id'),
            'orders_status_id' => CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
            'orders_status_invoice_id' => (int)$CLICSHOPPING_Order->info['order_status_invoice'],
            'admin_user_name' => '',
            'date_added' => 'now()',
            'customer_notified' => '0',
            'comments' => $comment_status
          ];

          $this->app->db->save('orders_status_history', $sql_data_array);
        }
      }
    }
  }