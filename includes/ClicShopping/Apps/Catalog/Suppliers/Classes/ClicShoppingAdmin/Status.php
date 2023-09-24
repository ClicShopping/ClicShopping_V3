<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{
  /**
   * Status products suppliers  - Sets the status of a product on suppliers
   * @param int $suppliers_id
   * @param int $status
   * @return int
   */
  public static function getSuppliersStatus(int $suppliers_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('suppliers', [
        'suppliers_status' => 1,
        'date_added' => 'null',
        'last_modified' => 'null'
      ],
        ['suppliers_id' => (int)$suppliers_id]
      );
    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('suppliers', [
        'suppliers_status' => 0,
        'last_modified' => 'now()'
      ],
        ['suppliers_id' => (int)$suppliers_id]
      );
    } else {
      return -1;
    }
  }
}