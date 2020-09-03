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


  namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions\Featured;

  use ClicShopping\OM\Registry;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Featured = Registry::get('Featured');

      if (isset($_GET['flag']) && isset($_GET['id'])) {
        static::getFeaturedProductsStatus($_GET['id'], $_GET['flag']);
      }

      $CLICSHOPPING_Featured->redirect('Featured', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'sID=' . (int)$_GET['id']);
    }

    /**
     * Status products featured products -  Sets the status of a favrite product
     * @param int $products_featured_id
     * @param int $status
     * @return int
     */
    Public static function getFeaturedProductsStatus(int $products_featured_id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {

        return $CLICSHOPPING_Db->save('products_featured', [
          'status' => 1,
          'scheduled_date' => 'null',
          'expires_date' => 'null',
          'date_status_change' => 'null'
        ],
          ['products_featured_id' => (int)$products_featured_id]
        );

      } elseif ($status == 0) {

        return $CLICSHOPPING_Db->save('products_featured', [
          'status' => 0,
          'date_status_change' => 'now()'
        ],
          ['products_featured_id' => (int)$products_featured_id]
        );

      } else {
        return -1;
      }
    }
  }