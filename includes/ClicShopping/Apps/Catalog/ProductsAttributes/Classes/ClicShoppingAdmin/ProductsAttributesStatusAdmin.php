<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class ProductsAttributesStatusAdmin
  {
    /**
     * Status products attibutes - Sets the status of a product
     * @param int $products_attributes_id
     * @param int $status
     * @return int
     */
    public static function getStatus(int $products_attributes_id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {
        return $CLICSHOPPING_Db->save('products_attributes', ['status' => 1],
          ['products_attributes_id' => (int)$products_attributes_id]
        );

      } elseif ($status == 0) {
        return $CLICSHOPPING_Db->save('products_attributes', ['status' => 0],
          ['products_attributes_id' => (int)$products_attributes_id]
        );

      } else {
        return -1;
      }
    }
  }