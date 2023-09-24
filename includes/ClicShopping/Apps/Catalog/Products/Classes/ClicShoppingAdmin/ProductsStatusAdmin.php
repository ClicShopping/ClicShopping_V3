<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class ProductsStatusAdmin
{
  /**
   * Status products - Sets the status of a product
   * @param int $products_id
   * @param int $status
   * @return int
   */
  public static function getProductStatus(int $products_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('products', [
        'products_status' => 1,
        'products_last_modified' => 'now()'
      ],
        ['products_id' => (int)$products_id]
      );

    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('products', [
        'products_status' => 0,
        'products_last_modified' => 'now()'
      ],
        ['products_id' => (int)$products_id]
      );

    } else {
      return -1;
    }
  }

  /**
   * @param int|null $products_id
   * @return bool
   */
  public static function checkProductStatus(?int $products_id): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qstatus = $CLICSHOPPING_Db->get('products', 'products_status', ['products_id' => $products_id]);

    if ($Qstatus->fetch()) {
      if ($Qstatus->valueInt('products_status') == 0) {
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }
}