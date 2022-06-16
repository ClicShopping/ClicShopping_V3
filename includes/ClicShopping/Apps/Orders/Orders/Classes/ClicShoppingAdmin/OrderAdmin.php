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

  namespace ClicShopping\Apps\Orders\Orders\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class OrderAdmin extends \ClicShopping\Apps\Orders\Orders\Classes\Shop\Order
  {
    public function __construct($order_id)
    {
      $this->info = [];
      $this->totals = [];
      $this->products = [];
      $this->customer = [];
      $this->delivery = [];

      $this->query($order_id);
    }

    public function query($order_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $Qorder = $CLICSHOPPING_Db->get([
        'orders o',
        'orders_status s'
      ], [
        'o.*',
        's.orders_status_name'
      ], [
          'o.orders_id' => (int)$order_id,
          'o.orders_status' => ['rel' => 's.orders_status_id'],
          's.language_id' => $CLICSHOPPING_Language->getId()
        ]
      );

      $Qtotals = $CLICSHOPPING_Db->get('orders_total', [
        'title',
        'text',
        'class'
      ], [
        'orders_id' => (int)$order_id
      ],
        'sort_order'
      );

      while ($Qtotals->fetch()) {
        $this->totals[] = [
          'title' => $Qtotals->value('title'),
          'text' => $Qtotals->value('text'),
          'class' => $Qtotals->value('class')
        ];
      }

      $this->info = [
        'id' => $Qorder->valueInt('orders_id'),
        'total' => null,
        'currency' => $Qorder->value('currency'),
        'currency_value' => $Qorder->value('currency_value'),
        'payment_method' => $Qorder->value('payment_method'),
        'cc_type' => $Qorder->value('cc_type'),
        'cc_owner' => $Qorder->value('cc_owner'),
        'cc_number' => $Qorder->value('cc_number'),
        'cc_expires' => $Qorder->value('cc_expires'),
        'date_purchased' => $Qorder->value('date_purchased'),
        'status' => $Qorder->value('orders_status_name'),
        'orders_status' => $Qorder->valueInt('orders_status'),
        'orders_status_invoice' => $Qorder->valueInt('orders_status_invoice'),
        'last_modified' => $Qorder->value('last_modified'),
        'erp_invoice' => $Qorder->valueInt('erp_invoice')
      ];

      foreach ($this->totals as $t) {
        if ($t['class'] == 'ot_total' || $t['class'] == 'TO') {
          $this->info['total'] = $t['text'];
          break;
        }
      }

      $this->customer = [
        'name' => $Qorder->value('customers_name'),
        'company' => $Qorder->value('customers_company'),
        'siret' => $Qorder->value('customers_siret'),
        'ape' => $Qorder->value('customers_ape'),
        'tva_intracom' => $Qorder->value('customers_tva_intracom'),
        'street_address' => $Qorder->value('customers_street_address'),
        'suburb' => $Qorder->value('customers_suburb'),
        'city' => $Qorder->value('customers_city'),
        'postcode' => $Qorder->value('customers_postcode'),
        'state' => $Qorder->value('customers_state'),
        'country' => $Qorder->value('customers_country'),
        'format_id' => $Qorder->value('customers_address_format_id'),
        'telephone' => $Qorder->value('customers_telephone'),
        'cellular_phone' => $Qorder->value('customers_cellular_phone'),
        'email_address' => $Qorder->value('customers_email_address'),
        'client_computer_ip' => $Qorder->value('client_computer_ip'),
        'provider_name_client' => $Qorder->value('provider_name_client')
      ];

      $this->delivery = [
        'name' => $Qorder->value('delivery_name'),
        'company' => $Qorder->value('delivery_company'),
        'street_address' => $Qorder->value('delivery_street_address'),
        'suburb' => $Qorder->value('delivery_suburb'),
        'city' => $Qorder->value('delivery_city'),
        'postcode' => $Qorder->value('delivery_postcode'),
        'state' => $Qorder->value('delivery_state'),
        'country' => $Qorder->value('delivery_country'),
        'format_id' => $Qorder->value('delivery_address_format_id')
      ];

      $this->billing = [
        'name' => $Qorder->value('billing_name'),
        'company' => $Qorder->value('billing_company'),
        'street_address' => $Qorder->value('billing_street_address'),
        'suburb' => $Qorder->value('billing_suburb'),
        'city' => $Qorder->value('billing_city'),
        'postcode' => $Qorder->value('billing_postcode'),
        'state' => $Qorder->value('billing_state'),
        'country' => $Qorder->value('billing_country'),
        'format_id' => $Qorder->value('billing_address_format_id')
      ];

      $index = 0;

      $Qproducts = $CLICSHOPPING_Db->get('orders_products', [
        'orders_products_id',
        'products_id',
        'products_name',
        'products_model',
        'products_price',
        'products_tax',
        'products_quantity',
        'final_price'
      ], [
          'orders_id' => (int)$order_id
        ]
      );

      while ($Qproducts->fetch()) {
        $this->products[$index] = [
          'orders_products_id' => $Qproducts->value('orders_products_id'),
          'qty' => $Qproducts->value('products_quantity'),
          'products_id' => $Qproducts->valueInt('products_id'),
          'name' => $Qproducts->value('products_name'),
          'model' => $Qproducts->value('products_model'),
          'tax' => $Qproducts->value('products_tax'),
          'price' => $Qproducts->valueDecimal('products_price'),
          'final_price' => $Qproducts->valueDecimal('final_price')
        ];

        $i = 0;

        $Qattributes = $CLICSHOPPING_Db->get('orders_products_attributes', [
          'products_options',
          'products_options_values',
          'options_values_price',
          'price_prefix',
          'products_attributes_reference'
        ], [
            'orders_id' => (int)$order_id,
            'orders_products_id' => $Qproducts->valueInt('orders_products_id')
          ]
        );
        $Qattributes->execute();

        if ($Qattributes->fetch() !== false) {
          do {
            $this->products[$index]['attributes'][$i] = [
              'option' => $Qattributes->value('products_options'),
              'value' => $Qattributes->value('products_options_values'),
              'prefix' => $Qattributes->value('price_prefix'),
              'price' => $Qattributes->value('options_values_price'),
              'reference' => $Qattributes->value('products_attributes_reference')
            ];

            $i++;
          } while ($Qattributes->fetch());
        }
        $index++;
      }

      $CLICSHOPPING_Hooks->call('OrderAdmin', 'Query');
    }


    /**
     * Remove order
     *
     * @param string $order_id , $restock
     * @return
     *
     */
    public static function removeOrder(int $order_id, bool $restock = false)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($restock)) {
        $Qproducts = $CLICSHOPPING_Db->get('orders_products', [
          'products_id',
          'products_quantity'
        ], [
            'orders_id' => (int)$order_id
          ]
        );

        while ($Qproducts->fetch()) {
          $Qupdate = $CLICSHOPPING_Db->prepare('update :table_products
                                                set products_quantity = products_quantity + ' . $Qproducts->valueInt('products_quantity') . ',
                                                products_ordered = products_ordered - ' . $Qproducts->valueInt('products_quantity') . '
                                                where products_id = :products_id
                                               ');
          $Qupdate->bindInt(':products_id', $Qproducts->valueInt('products_id'));
          $Qupdate->execute();
        }

        $CLICSHOPPING_Db->delete('products_groups', ['products_id' => (int)$Qproducts->value('products_id')]);
      }

      $CLICSHOPPING_Db->delete('orders', ['orders_id' => (int)$order_id]);
      $CLICSHOPPING_Db->delete('orders_products', ['orders_id' => (int)$order_id]);
      $CLICSHOPPING_Db->delete('orders_products_attributes', ['orders_id' => (int)$order_id]);
      $CLICSHOPPING_Db->delete('orders_status_history', ['orders_id' => (int)$order_id]);
      $CLICSHOPPING_Db->delete('orders_total', ['orders_id' => (int)$order_id]);
      $CLICSHOPPING_Db->delete('orders_pages_manager', ['orders_id' => (int)$order_id]);

      $CLICSHOPPING_Hooks->call('OrderAdmin', 'removeOrder');
    }


    /**
     * the name order status
     *
     * @param string
     * @return string orders_status_array,  name of the order status
     *
     */

    public static function getOrdersStatus(): array
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Db = Registry::get('Db');

      $orders_status_array = [];

      $Qstatus = $CLICSHOPPING_Db->get('orders_status', ['orders_status_id',
        'orders_status_name'
      ], [
        'language_id' => (int)$CLICSHOPPING_Language->getId()
      ],
        'orders_status_id'
      );

      while ($Qstatus->fetch()) {
        $orders_status_array[] = ['id' => $Qstatus->valueInt('orders_status_id'),
          'text' => $Qstatus->value('orders_status_name')
        ];
      }

      return $orders_status_array;
    }

    /*
     * pdf logo
     * return string or bool
     */
    public static function getOrderPdfInvoiceLogo(): string|bool
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (is_file($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . 'logos/invoice/' . INVOICE_LOGO)) {
        $result = $CLICSHOPPING_Template->getDirectoryShopTemplateImages() . 'logos/invoice/' . INVOICE_LOGO;
      } else {
        $result = false;
      }

      return $result;
    }
  }