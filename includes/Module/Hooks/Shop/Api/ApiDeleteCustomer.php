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

  class ApiDeleteCustomer
  {
    /**
     * @param int $id
     * @return void
     */
    private static function deleteCustomer(int $id) :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $Qcheck = $CLICSHOPPING_Db->prepare('select customers_id
                                           from :table_customers
                                           where customers_id = :customers_id
                                          ');

      $Qcheck->bindInt(':customers_id', $id);
      $Qcheck->execute();

      if ($Qcheck->fetch()) {
        $sql_array = [
          'customers_id' => (int)$id,
        ];

        $CLICSHOPPING_Db->delete('customers', $sql_array);
        $CLICSHOPPING_Db->delete('address_book',$sql_array);
        $CLICSHOPPING_Db->delete('customers_info', ['customers_info_id' => $id]);
        $CLICSHOPPING_Db->delete('customers_basket', $sql_array);
        $CLICSHOPPING_Db->delete('customers_basket_attributes', $sql_array);

        $CLICSHOPPING_Hooks->call('Customers', 'DeleteCustomers');
      }
    }

    public function execute()
    {
      if (isset($_GET['cId'], $_GET['customer'])) {
        $id = HTML::sanitize($_GET['cId']);

        return static::deleteCustomer($id);
      } else {
        return false;
      }
    }
  }