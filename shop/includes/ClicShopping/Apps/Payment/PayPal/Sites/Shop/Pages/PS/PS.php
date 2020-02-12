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

  namespace ClicShopping\Apps\Payment\PayPal\Sites\Shop\Pages\PS;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop\TemplateEmail;

  use ClicShopping\Apps\Payment\PayPal\Module\Payment\PS as PaymentModulePS;

  class PS extends \ClicShopping\OM\PagesAbstract
  {
    protected $file = null;
    protected $use_site_template = false;
    protected $pm;
    protected $lang;

    protected function init()
    {

      $this->lang = Registry::get('Language');

      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Prod = Registry::get('Prod');
      $CLICSHOPPING_Address = Registry::get('Address');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $this->pm = new PaymentModulePS();

      if (!defined('CLICSHOPPING_APP_PAYPAL_PS_STATUS') || !in_array(CLICSHOPPING_APP_PAYPAL_PS_STATUS, [
          '1',
          '0'
        ])) {
        return false;
      }

      $this->lang->loadDefinitions('Shop/checkout_process');

      $result = false;

      $seller_accounts = [$this->pm->app->getCredentials('PS', 'email')];

      if (!is_null($this->pm->app->getCredentials('PS', 'email_primary'))) {
        $seller_accounts[] = $this->pm->app->getCredentials('PS', 'email_primary');
      }

      if ((isset($_POST['receiver_email']) && in_array($_POST['receiver_email'], $seller_accounts)) || (isset($_POST['business']) && in_array($_POST['business'], $seller_accounts)) || (isset($_POST['receiver_id']) && in_array($_POST['receiver_id'], $seller_accounts))) {
        $parameters = 'cmd=_notify-validate&';

        foreach ($_POST as $key => $value) {
          if ($key != 'cmd') {
            $parameters .= $key . '=' . urlencode(stripslashes($value)) . '&';
          }
        }

        $parameters = substr($parameters, 0, -1);

        $result = $this->pm->app->makeApiCall($this->pm->form_action_url, $parameters);
      }

      $log_params = $_POST;
      $log_params['cmd'] = '_notify-validate';

      foreach ($_GET as $key => $value) {
        $log_params['GET ' . $key] = stripslashes($value);
      }

      $this->pm->app->log('PS', '_notify-validate', ($result == 'VERIFIED') ? 1 : -1, $log_params, $result, (CLICSHOPPING_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox', true);

      if ($result == 'VERIFIED') {
        $this->pm->verifyTransaction($_POST, true);

        $order_id = (int)$_POST['invoice'];
        $customer_id = (int)$_POST['custom'];

        $Qorder = $this->pm->app->db->get('orders', 'orders_status', ['orders_id' => (int)$order_id,
            'customers_id' => (int)$customer_id,
          ]
        );

        if ($Qorder->fetch() !== false) {
          if ($Qorder->valueInt('orders_status') == CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID) {
            $new_order_status = DEFAULT_ORDERS_STATUS_ID;

            if (CLICSHOPPING_APP_PAYPAL_PS_ORDER_STATUS_ID > 0) {
              $new_order_status = CLICSHOPPING_APP_PAYPAL_PS_ORDER_STATUS_ID;
            }

            $this->pm->app->db->save('orders', ['orders_status' => (int)$new_order_status,
              'last_modified' => 'now()'
            ], [
                'orders_id' => (int)$order_id
              ]
            );

            $sql_data_array = ['orders_id' => (int)$order_id,
              'orders_status_id' => (int)$new_order_status,
              'orders_status_invoice_id' => 1,
              'admin_user_name' => '',
              'date_added' => 'now()',
              'customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0',
              'comments' => ''
            ];

            $this->pm->app->db->save('orders_status_history', $sql_data_array);

            $CLICSHOPPING_Order = Registry::get('Order');

            if (DOWNLOAD_ENABLED == 'true') {
              for ($i = 0, $n = count($CLICSHOPPING_Order->products); $i < $n; $i++) {
                $Qdownloads = $this->pm->app->db->prepare('select opd.orders_products_filename
                                                           from :table_orders o,
                                                                :table_orders_products op,
                                                                :table_orders_products_download opd
                                                          where o.orders_id = :order_id
                                                          and o.customers_id = :customers_id
                                                          and o.orders_id = op.orders_id
                                                          and op.orders_products_id = opd.orders_products_id
                                                          and opd.orders_products_filename <> :orders_products_filename
                                                         ');
                $Qdownloads->bindInt(':orders_id', $order_id);
                $Qdownloads->bindInt(':customers_id', $customer_id);
                $Qdownloads->bindValue(':orders_products_filename', '');

                $Qdownloads->execute();

                if ($Qdownloads->fetch() !== false) {
                  if ($CLICSHOPPING_Order->content_type == 'physical') {
                    $CLICSHOPPING_Order->content_type = 'mixed';

                    break;
                  } else {
                    $CLICSHOPPING_Order->content_type = 'virtual';
                  }
                } else {
                  if ($CLICSHOPPING_Order->content_type == 'virtual') {
                    $CLICSHOPPING_Order->content_type = 'mixed';

                    break;
                  } else {
                    $CLICSHOPPING_Order->content_type = 'physical';
                  }
                }
              }
            } else {
              $CLICSHOPPING_Order->content_type = 'physical';
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

                    $products_attributes = $this->products['attributes'] ?? '';

                    if (is_array($products_attributes)) {
                      $stock_query_sql .= ' and pa.options_id = :options_id
                                           and pa.options_values_id = :options_values_id
                                        ';
                    }

                    $Qstock = $this->pm->app->db->prepare($stock_query_sql);

                    $Qstock->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));

                    if (is_array($products_attributes)) {
                      $Qstock->bindInt(':options_id', $products_attributes['option_id']);
                      $Qstock->bindInt(':options_values_id', $products_attributes['value_id']);
                    }

                    $Qstock->execute();
                  } else {
                     $Qstock = $this->pm->app->db->prepare('select products_quantity
                                                  products_quantity_alert
                                          from :table_products
                                          where products_id = :products_id
                                          ');

                    $Qstock->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));
                    $Qstock->execute();
                  }


// select the good qty in B2B ti decrease the stock. See shopping_cart top display out stock or not
                if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
                  $QproductsQuantityCustomersGroup = $this->pm->app->db->prepare('select products_quantity_fixed_group
                                                                        from :table_products_groups
                                                                        where products_id = :products_id
                                                                        and customers_group_id =  :customers_group_id
                                                                       ');
                  $QproductsQuantityCustomersGroup->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));
                  $QproductsQuantityCustomersGroup->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
                  $QproductsQuantityCustomersGroup->execute();

                  $products_quantity_customers_group = $QproductsQuantityCustomersGroup->fetch();

// do the exact qty in function the customer group and product
                  $products_quantity_customers_group = $products_quantity_customers_group['products_quantity_fixed_group'];
                } else {
                  $products_quantity_customers_group = 1;
                }


                if (DOWNLOAD_ENABLED == 'true') {
                  $stock_query_sql = 'select p.products_quantity,
                                                pad.products_attributes_filename
                                        from :table_products p
                                          left join :table_products_attributes pa on p.products_id = pa.products_id
                                          left join :table_products_attributes_download pad
                                        on pa.products_attributes_id = pad.products_attributes_id
                                        where p.products_id = :products_id
                                        ';

// Will work with only one option for downloadable products
// otherwise, we have to build the query dynamically with a loop
                  $products_attributes = $CLICSHOPPING_Order->products[$i]['attributes'] ?? '';

                  if (is_array($products_attributes)) {
                    $stock_query_sql .= ' and pa.options_id = :options_id
                                            and pa.options_values_id = :options_values_id';
                  }

                  $Qstock = $this->pm->app->db->prepare($stock_query_sql);
                  $Qstock->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));

                  if (is_array($products_attributes)) {
                    $Qstock->bindInt(':options_id', $products_attributes[0]['option_id']);
                    $Qstock->bindInt(':options_values_id', $products_attributes[0]['value_id']);
                  }

                  $Qstock->execute();
                } else {
                  $Qstock = $this->pm->app->db->get('products', 'products_quantity', ['products_id' => $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id'])]);
                }

                if ($Qstock->fetch() !== false) {
// do not decrement quantities if products_attributes_filename exists
                  if ((DOWNLOAD_ENABLED != 'true') || !empty($Qstock->value('products_attributes_filename'))) {
                    if (STOCK_ALLOW_CHECKOUT == 'false') {
                      $stock_left = $Qstock->valueInt('products_quantity') - ($CLICSHOPPING_Order->products[$i]['qty']) * $products_quantity_customers_group;
                    } else {
                      $stock_left = $Qstock->valueInt('products_quantity');
                    }
                  } else {
                    $stock_left = $Qstock->valueInt('products_quantity');
                  }

                  if ($stock_left != $Qstock->valueInt('products_quantity')) {
                    $this->pm->app->db->save('products', ['products_quantity' => $stock_left
                    ], [
                        'products_id' => $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id'])
                      ]
                    );
                  }

                  if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
                    $this->pm->app->db->save('products', ['products_status' => '0'
                    ], [
                        'products_id' => $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id'])
                      ]
                    );
                  }
                }
              }

// Update products_ordered (for bestsellers list)

              $Qupdate = $this->pm->app->db->prepare('update :table_products
                                                      set products_ordered = products_ordered + :products_ordered
                                                      where products_id = :products_id
                                                      ');
              $Qupdate->bindInt(':products_ordered', $CLICSHOPPING_Order->products[$i]['qty']);
              $Qupdate->bindInt(':products_id', $CLICSHOPPING_Prod::getProductID($CLICSHOPPING_Order->products[$i]['id']));
              $Qupdate->execute();

              $products_ordered_attributes = '';

              if (isset($CLICSHOPPING_Order->products[$i]['attributes'])) {
                for ($j = 0, $n2 = count($CLICSHOPPING_Order->products[$i]['attributes']); $j < $n2; $j++) {
                  $products_ordered_attributes .= "\n\t" . $CLICSHOPPING_Order->products[$i]['attributes'][$j]['option'] . ' ' . $CLICSHOPPING_Order->products[$i]['attributes'][$j]['value'];
                }
              }

//------insert customer choosen option eof ----
              $products_ordered .= $CLICSHOPPING_Order->products[$i]['qty'] . ' x ' . $CLICSHOPPING_Order->products[$i]['name'] . ' (' . $CLICSHOPPING_Order->products[$i]['model'] . ') = ' . $CLICSHOPPING_Currencies->displayPrice($CLICSHOPPING_Order->products[$i]['final_price'], $CLICSHOPPING_Order->products[$i]['tax'], $CLICSHOPPING_Order->products[$i]['qty']) . $products_ordered_attributes . "\n";
              $products_ordered = html_entity_decode($products_ordered);
            }

//*******************************
// email
//*******************************

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
            $email_order .= CLICSHOPPING::getDef('email_text_payment_method') . "\n\n" .
              CLICSHOPPING::getDef('email_separator') . "\n" .
              $this->pm->public_title . "\n\n";

            if (isset($this->pm->email_footer)) {
              $email_order .= $this->pm->email_footer . "\n\n";
            }

            $email_order .= TemplateEmail::getTemplateEmailSignature() . "\n\n";
            $email_order .= TemplateEmail::getTemplateEmailTextFooter() . "\n\n";

            $CLICSHOPPING_Mail->clicMail($CLICSHOPPING_Order->customer['name'], $CLICSHOPPING_Order->customer['email_address'], CLICSHOPPING::getDef('email_text_subject', ['store_name' => STORE_NAME]), $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

// send emails to other people
// SEND_EXTRA_ORDER_EMAILS_TO does'nt work like this, test<test@test.com>, just with test@test.com
            if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
              $email_text_subject = stripslashes(CLICSHOPPING::getDef('email_text_subject', ['store_name' => STORE_NAME]));
              $email_text_subject = html_entity_decode($email_text_subject);

              $text[] = TemplateEmail::getExtractEmailAddress(SEND_EXTRA_ORDER_EMAILS_TO);

              foreach ($text as $key => $email) {
                $CLICSHOPPING_Mail->clicMail('', $email[$key], $email_text_subject, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
              }
            }

            $this->pm->app->db->delete('customers_basket', ['customers_id' => (int)$customer_id]);

            $this->pm->app->db->delete('customers_basket_attributes', ['customers_id' => (int)$customer_id]);
          }
        }
      }

      Registry::get('Session')->kill();
    }
  }
