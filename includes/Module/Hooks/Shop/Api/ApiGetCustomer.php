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

  class ApiGetCustomer
  {
    /**
     * @param int|string $id
     * @return array
     */
    private static function getcustomer(int|string $id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (is_numeric($id)) {
        $sql_request = ' and c.customers_id = :customers_id';
      } else {
        $sql_request = '';
      }

      $Qapi = $CLICSHOPPING_Db->prepare('select c.*,
                                                 a.*
                                          from :table_customers c left join :table_address_book a on c.customers_default_address_id = a.address_book_id
                                          where a.customers_id = c.customers_id
                                          ' . $sql_request . '
                                        ');

      if (is_numeric($id)) {
        $Qapi->bindInt(':customers_id', $id);
     }

      $Qapi->execute();

      $customer_data = [];

      $result = $Qapi->fetchAll();

      foreach ($result as $value) {
        $customer_data[] = [
          'customers_id'                => $value['customers_id'],
          'customers_company'           => $value['customers_company'],
          'customers_gender'            => $value['customers_gender'],
          'customers_firstname'    	    => $value['customers_firstname'],
          'customers_lastname'          => $value['customers_lastname'],
          'customers_dob'               => $value['customers_dob'],
          'customers_email_address'          => $value['customers_email_address'],
          'customers_default_address_id'     => $value['customers_default_address_id'],
          'customers_telephone'           => $value['customers_telephone'],
          'customers_newsletter'          => $value['customers_newsletter'],
          'languages_id'                  => $value['languages_id'],
          'entry_street_address'          => $value['entry_street_address'],
          'entry_suburb'        => $value['entry_suburb'],
          'entry_postcode'      => $value['entry_postcode'],
          'entry_city'          => $value['entry_city'],
          'entry_state'         => $value['entry_state'],
          'entry_country_id'    => $value['entry_country_id'],
          'entry_zone_id'       => $value['entry_zone_id'],
        ];
      }

      return $customer_data;
    }

    public function execute()
    {
      if (isset($_GET['cId'], $_GET['token'])) {
        $id = HTML::sanitize($_GET['cId']);

        return static::getcustomer($id);
      } else {
        return false;
      }
    }
  }
