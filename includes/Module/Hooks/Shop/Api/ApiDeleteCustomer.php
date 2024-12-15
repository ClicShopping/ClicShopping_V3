<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Api;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ApiDeleteCustomer
{
  /**
   * Deletes a customer and all associated information from the database.
   *
   * @param int $id The ID of the customer to be deleted.
   * @return void
   */
  private static function deleteCustomer(int $id): void
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
      $CLICSHOPPING_Db->delete('address_book', $sql_array);
      $CLICSHOPPING_Db->delete('customers_info', ['customers_info_id' => $id]);
      $CLICSHOPPING_Db->delete('customers_basket', $sql_array);
      $CLICSHOPPING_Db->delete('customers_basket_attributes', $sql_array);

      $CLICSHOPPING_Hooks->call('Customers', 'DeleteCustomers');
    }
  }

  /**
   * Executes the operation to delete a customer based on the provided ID.
   *
   * @return false|string Returns a JSON-encoded error message if the ID is invalid,
   *                      false if the required parameters are missing,
   *                      or performs the customer deletion if valid input is provided.
   */
  public function execute()
  {
    if (isset($_GET['cId'], $_GET['customer'])) {
      $id = HTML::sanitize($_GET['cId']);

      if (!is_numeric($id)) {
        http_response_code(400);
        return json_encode(['error' => 'Invalid ID format']);
      }

      static::deleteCustomer($id);
    } else {
      return false;
    }
  }
}