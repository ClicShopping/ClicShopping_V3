<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\Apps\Catalog\Categories\Categories as CategoriesApp;
use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

/**
 * Class responsible
 * for managing and displaying statistics related to categories within the application.
 */
class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Constructor method for the class.
   *
   * Initializes the Categories application module by checking its existence in the Registry.
   * If not present, it creates a new instance of CategoriesApp and stores it in the Registry.
   * Assigns the app instance from the Registry to the $this->app property.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Categories')) {
      Registry::set('Categories', new CategoriesApp());
    }

    $this->app = Registry::get('Categories');
  }

  /**
   * Saves the product category association and clears relevant cache entries.
   *
   * @param array $current_category_id An array containing the current category ID(s).
   * @return void
   */
  private function saveProductCategory(array $current_category_id): void
  {
    if (isset($_GET['Insert'])) {
      $current_category_id = $current_category_id[0];

      $Qproducts = $this->app->db->prepare('select products_id
                                              from :table_products
                                              order by products_id desc
                                              limit 1
                                              ');
      $Qproducts->execute();

      $id = $Qproducts->valueInt('products_id');

      $sql_array = [
        'products_id' => (int)$id,
        'categories_id' => (int)$current_category_id
      ];

      $this->app->db->save('products_to_categories', $sql_array);
    }

    Cache::clear('categories');
    Cache::clear('products-also_purchased');
    Cache::clear('products_related');
    Cache::clear('products_cross_sell');
    Cache::clear('upcoming');
  }

  /**
   * Executes the operation to handle category movement for a product.
   *
   * @return bool Returns false if the application status is either undefined or set to 'False'.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
      return false;
    }

    if (isset($_POST['move_to_category_id'])) {
      $current_category_id = HTML::sanitize($_POST['move_to_category_id']);

      $this->saveProductCategory($current_category_id);
    }
  }
}