<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;
    protected $ID;
    protected $cPath;
    protected $productCategoriesId;

    public function __construct(){
      $this->app = Registry::get('Products');

      $this->ID = HTML::sanitize($_POST['products_id']);
      $this->productCategoriesId = $_POST['product_categories'];
      $this->cPath = HTML::sanitize($_GET['cPath']);
    }

    public function execute()  {
      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

      if (isset($this->ID) && isset($this->productCategoriesId) && is_array($this->productCategoriesId)) {

        $products_categories_id = count($this->productCategoriesId);

        for ($i=0, $n = $products_categories_id; $i<$n; $i++) {
// delete product of categorie
          $this->app->db->delete('products_to_categories', [
                                                            'products_id' => (int)$this->ID,
                                                            'categories_id' => (int)$this->productCategoriesId[$i]
                                                            ]
                                );

          $this->app->db->delete('products_notifications', ['products_id' => (int)$this->ID] );
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