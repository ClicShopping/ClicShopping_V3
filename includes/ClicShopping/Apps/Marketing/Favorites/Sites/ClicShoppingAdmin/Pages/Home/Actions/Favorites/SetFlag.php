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


  namespace ClicShopping\Apps\Marketing\Favorites\Sites\ClicShoppingAdmin\Pages\Home\Actions\Favorites;

  use ClicShopping\OM\Registry;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Favorites = Registry::get('Favorites');

      if (isset($_GET['flag'], $_GET['id'])) {
        static::getFavoritesProductsStatus($_GET['id'], $_GET['flag']);
      }

      $CLICSHOPPING_Favorites->redirect('Favorites', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'sID=' . (int)$_GET['id']);
    }

    /**
     * Status products favorites products -  Sets the status of a favrite product
     * @param $products_favorites_id
     * @param $status
     * @return int
     */
    Public static function getFavoritesProductsStatus($products_favorites_id, $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {
        return $CLICSHOPPING_Db->save('products_favorites', ['status' => 1,
          'scheduled_date' => 'null',
          'expires_date' => 'null',
          'date_status_change' => 'null'
        ],
          ['products_favorites_id' => (int)$products_favorites_id]
        );
      } elseif ($status == 0) {
        return $CLICSHOPPING_Db->save('products_favorites', ['status' => 0,
          'date_status_change' => 'now()'
        ],
          ['products_favorites_id' => (int)$products_favorites_id]
        );
      } else {
        return -1;
      }
    }
  }