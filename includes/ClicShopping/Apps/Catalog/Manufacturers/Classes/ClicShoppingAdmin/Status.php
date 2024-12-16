<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

/**
 * Class Status provides functionalities to manage the status of manufacturers and products.
 */
class Status
{
  /**
   * Updates the status of a manufacturer in the database based on the provided parameters.
   *
   * @param int $manufacturers_id The ID of the manufacturer to be updated.
   * @param int $status The desired status to set for the manufacturer (1 for active, 0 for inactive).
   * @return mixed Returns the result of the database save operation if successful, or -1 if an invalid status is provided.
   */
  public static function getManufacturersStatus(int $manufacturers_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == '1') {
      $update_array = [
        'manufacturers_status' => 1,
        'date_added' => 'null',
        'last_modified' => 'null'
      ];

      return $CLICSHOPPING_Db->save('manufacturers', $update_array, ['manufacturers_id' => (int)$manufacturers_id]);
    } elseif ($status == '0') {
      $update_array = [
        'manufacturers_status' => 0,
        'last_modified' => 'now()'
      ];

      return $CLICSHOPPING_Db->save('manufacturers', $update_array, ['manufacturers_id' => (int)$manufacturers_id]);
    } else {
      return -1;
    }
  }
}