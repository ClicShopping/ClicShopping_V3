<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\Shop\Api;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class ApiGetOrder
  {
    /**
     * @param int|string $id
     * @return array
     */
    private static function getOrder(int|string $id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (is_numeric($id)) {
        $sql_request = ' and o.orders_id = :orders_id';
      } else {
        $sql_request = '';
      }
      $Qapi = $CLICSHOPPING_Db->prepare('select c.*,
                                                o.*,
                                                ot.*
                                        from :table_customers c,
                                             :table_orders o,
                                             :table_orders_total ot
                                        where c.customers_id = o.customers_id
                                        and o.orders_id = ot.orders_id
                                        ' . $sql_request . '
                                        limit 1
                                        ');


      if (is_numeric($id)) {
        $Qapi->bindInt(':orders_id', $id);
     }

      $Qapi->execute();

      $customer_data = [];

      $result = $Qapi->fetchAll();

      foreach ($result as $value) {
        $customer_data[] = [
          'orders_id'                    => $value['orders_id'],
          'customers_id'                  => $value['customers_id'],
          'customers_company'             => $value['customers_company'],
          'customers_siret'               => $value['customers_siret'],
          'customers_ape'                 => $value['customers_ape'],
          'customers_tva_intracom'        => $value['customers_tva_intracom'],
          'customers_gender'              => $value['customers_gender'],
          'customers_firstname'           => $value['customers_firstname'],
          'customers_lastname'            => $value['customers_lastname'],
          'customers_email_address'       => $value['customers_email_address'],
          'customers_default_address_id'  => $value['customers_default_address_id'],
          'customers_telephone'           => $value['customers_telephone'],
          'customers_street_address'      => $value['customers_street_address'],
          'customers_suburb'              => $value['customers_suburb'],
          'customers_city'                => $value['customers_city'],
          'customers_postcode'            => $value['customers_postcode'],
          'customers_state'               => $value['customers_state'],
          'customers_country'             => $value['customers_country'],
//delivery
          'delivery_name'                 => $value['delivery_name'],
          'delivery_company'              => $value['delivery_company'],
          'delivery_street_address'       => $value['delivery_street_address'],
          'delivery_suburb'               => $value['delivery_suburb'],
          'delivery_city'                 => $value['delivery_city'],
          'delivery_postcode'             => $value['delivery_postcode'],
          'delivery_state'                => $value['delivery_state'],
          'delivery_country'              => $value['delivery_country'],
//payment
          'billing_name'                 => $value['billing_name'],
          'billing_company'              => $value['billing_company'],
          'billing_street_address'       => $value['billing_street_address'],
          'billing_suburb'               => $value['billing_suburb'],
          'billing_city'                 => $value['billing_city'],
          'billing_postcode'             => $value['billing_postcode'],
          'billing_state'                => $value['billing_state'],
          'billing_country'              => $value['billing_country'],
          'payment_method'               => $value['payment_method'],
//order
          'date_modifed'                 => $value['billing_country'],
          'date_purchased'               => $value['date_purchased'],
          'orders_status'                => $value['orders_status'],
          'orders_status_invoice'        => $value['orders_status_invoice'],
          'orders_date_finished'         => $value['orders_date_finished'],
//currency
          'currency'                     => $value['currency'],
          'currency_value'               => $value['currency_value'],
          'client_computer_ip'           => $value['client_computer_ip'],
          'provider_name_client'          => $value['provider_name_client'],
          'customers_cellular_phone'     => $value['customers_cellular_phone'],
//totalOrders
          'title'               => $value['title'],
          'text'                => str_replace('&nbsp;', '',$value['text']),
          'value'               => $value['value'],
        ];
      }

      return $customer_data;
    }

    public function execute()
    {
      if (isset($_GET['oId'], $_GET['token'])) {
        $id = HTML::sanitize($_GET['oId']);

        return static::getOrder($id);
      } else {
        return false;
      }
    }
  }
