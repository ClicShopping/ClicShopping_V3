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

class ApiDeleteManufacturer
{
  /**
   * Deletes a manufacturer and its related data from the database.
   *
   * This method removes a manufacturer record and its associated information from the database.
   * It also updates products to reset their manufacturer reference and sets their status to inactive.
   * Finally, it triggers any hooks associated with the manufacturer deletion.
   *
   * @param int $id The unique identifier of the manufacturer to be deleted.
   * @return void
   */
  private static function deleteManufacturer(int $id)
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $Qcheck = $CLICSHOPPING_Db->prepare('select manufacturers_id
                                           from :table_manufacturers
                                           where manufacturers_id = :manufacturers_id
                                          ');

    $Qcheck->bindInt(':manufacturers_id', $id);
    $Qcheck->execute();

    if ($Qcheck->fetch()) {
      $sql_array = [
        'manufacturers_id' => (int)$id,
      ];

      $CLICSHOPPING_Db->delete('manufacturers', $sql_array);
      $CLICSHOPPING_Db->delete('manufacturers_info', $sql_array);

      $Qupdate = $CLICSHOPPING_Db->prepare('update :table_products
                                              set manufacturers = :manufacturers,
                                                  products_status = 0
                                              where manufacturers = :manufacturers1
                                            ');
      $Qupdate->bindInt(':manufacturers', '');
      $Qupdate->bindInt(':manufacturers1', $id);

      $Qupdate->execute();

      $CLICSHOPPING_Hooks->call('Manufacturers', 'Delete');
    }
  }

  /**
   * Executes the deletion process for a manufacturer if the required parameters are present and valid.
   *
   * @return mixed Returns the result of the delete operation if successful, a JSON-encoded error message if the ID format is invalid, or false if necessary parameters are missing.
   */
  public function execute()
  {
    if (isset($_GET['mId'], $_GET['manufacturers'])) {
      $id = HTML::sanitize($_GET['mId']);

      if (!is_numeric($id)) {
        http_response_code(400);
        return json_encode(['error' => 'Invalid ID format']);
      }

      return self::deleteManufacturer($id);
    } else {
      return false;
    }
  }
}