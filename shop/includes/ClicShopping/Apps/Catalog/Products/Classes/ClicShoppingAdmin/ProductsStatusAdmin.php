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

  namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class ProductsStatusAdmin
  {

    /**
     * Status products - Sets the status of a product
     *
     * @param string products_id, status
     * @return string status on or off
     * @access public
     */

    public static function getProductStatus($products_id, $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {
        return $CLICSHOPPING_Db->save('products', ['products_status' => 1,
          'products_last_modified' => 'now()'
        ],
          ['products_id' => (int)$products_id]
        );

      } elseif ($status == 0) {
        return $CLICSHOPPING_Db->save('products', ['products_status' => 0,
          'products_last_modified' => 'now()'
        ],
          ['products_id' => (int)$products_id]
        );

      } else {
        return -1;
      }
    }
  }