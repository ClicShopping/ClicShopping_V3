<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{
  protected $status;
  protected $manufacturers_id;

  /**
   * Status products manufacturers  - Sets the status of a product on manufacturers
   * @param int $manufacturers_id
   * @param int $status
   * @return int
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

      return $CLICSHOPPING_Db->save('manufacturers', $update_array, ['manufacturers_id' => (int)$manufacturers_id]
      );
    } else {
      return -1;
    }
  }
}