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

class ApiDeleteSupplier
{
  /**
   * Deletes a supplier and associated data from the database.
   *
   * Deletes records from the suppliers table, suppliers_info table, and updates
   * affected products in the database. Additionally, triggers any associated
   * hooks for supplier deletion.
   *
   * @param int $id The ID of the supplier to delete.
   * @return void
   */
  private static function deleteSupplier(int $id): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $Qcheck = $CLICSHOPPING_Db->prepare('select suppliers_id
                                           from :table_suppliers
                                           where suppliers_id = :suppliers_id
                                          ');

    $Qcheck->bindInt(':suppliers_id', $id);
    $Qcheck->execute();

    if ($Qcheck->fetch()) {
      $sql_array = [
        'suppliers_id' => (int)$id,
      ];

      $CLICSHOPPING_Db->delete('suppliers', $sql_array);
      $CLICSHOPPING_Db->delete('suppliers_info', $sql_array);

      $Qupdate = $CLICSHOPPING_Db->prepare('update :table_products
                                              set suppliers_id = :suppliers_id,
                                                  products_status = 0
                                              where suppliers_id = :suppliers_id1
                                            ');
      $Qupdate->bindInt(':suppliers_id', '');
      $Qupdate->bindInt(':suppliers_id1', $id);

      $Qupdate->execute();

      $CLICSHOPPING_Hooks->call('Suppliers', 'Delete');
    }
  }

  /**
   * Executes the main logic to delete a supplier based on the provided ID.
   *
   * Checks if the necessary parameters are passed via the GET request. Validates the supplier ID
   * format and sanitizes it. Calls the static method to perform the deletion of the supplier.
   *
   * @return false|string False if required parameters are missing; JSON-encoded error message if
   *                      the ID format is invalid; otherwise, no return value.
   */
  public function execute()
  {
    if (isset($_GET['sId'], $_GET['suppliers'])) {
      $id = HTML::sanitize($_GET['sId']);

      if (!is_numeric($id)) {
        http_response_code(400);
        return json_encode(['error' => 'Invalid ID format']);
      }

      static::deleteSupplier($id);
    } else {
      return false;
    }
  }
}