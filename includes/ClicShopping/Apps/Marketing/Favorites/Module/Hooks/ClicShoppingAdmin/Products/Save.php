<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Favorites\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

class Save implements \ClicShopping\OM\Modules\HooksInterface
{
  private mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Favorites')) {
      Registry::set('Favorites', new FavoritesApp());
    }

    $this->app = Registry::get('Favorites');
  }

  /**
   * @param int $id
   */
  private function saveProductsFavorites(int $id): void
  {
    if (!empty($_POST['products_favorites'])) {
      $inset_array = [
        'products_id' => (int)$id,
        'products_favorites_date_added' => 'now()',
        'status' => 1,
        'customers_group_id' => 0
      ];

      $this->app->db->save('products_favorites', $inset_array);
    }
  }

  /**
   * @param int $id
   */
  private function save(int $id)
  {
    $this->saveProductsFavorites($id);
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_FAVORITES_FA_STATUS') || CLICSHOPPING_APP_FAVORITES_FA_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['pID'])) {
      $id = HTML::sanitize($_GET['pID']);
      $this->save($id);
    } else {
      if (!empty($_POST['products_favorites'])) {
        $Qproducts = $this->app->db->prepare('select products_id
                                                from :table_products
                                                order by products_id desc
                                                limit 1
                                               ');
        $Qproducts->execute();

        $inset_array = [
          'products_id' => (int)$Qproducts->valueInt('products_id'),
          'products_favorites_date_added' => 'now()',
          'status' => 1,
          'customers_group_id' => 0
        ];

        $this->app->db->save('products_favorites', $inset_array);
      }
    }
  }
}