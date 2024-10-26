<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;
  protected $ID;
  protected $cPath;
  protected $productCategoriesId;

  public function __construct()
  {
    $this->app = Registry::get('Products');

    $this->ID = HTML::sanitize($_POST['products_id']);
    $this->productCategoriesId = $_POST['product_categories'];
    $this->cPath = HTML::sanitize($_GET['cPath']);
  }

  public function execute()
  {
    $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

    if (isset($this->ID) && isset($this->productCategoriesId) && \is_array($this->productCategoriesId)) {

      $products_categories_id = \count($this->productCategoriesId);

      for ($i = 0, $n = $products_categories_id; $i < $n; $i++) {
// delete product of categorie
        $sql_array = [
          'products_id' => (int)$this->ID,
          'categories_id' => (int)$this->productCategoriesId[$i]
        ];

        $this->app->db->delete('products_to_categories', $sql_array);

        $this->app->db->delete('products_notifications', ['products_id' => (int)$this->ID]);
      } // end for

      $Qcheck = $this->app->db->get('products_to_categories', 'products_id', ['products_id' => (int)$this->ID], null, 1);

      if ($Qcheck->fetch() === false) {
        $CLICSHOPPING_ProductsAdmin->removeProduct($this->ID);
      }
    }

    Cache::clear('categories');
    Cache::clear('products-also_purchased');
    Cache::clear('upcoming');

    $this->app->redirect('Products&cPath=' . $this->cPath);
  }
}