<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin;

  class CopyConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;
    protected $ID;
    protected $categoriesId;
    protected $currentCategoryId;
    protected $copyAs;
    protected $productsAdmin;

    public function __construct(){
      $this->app = Registry::get('Products');

      $this->ID = HTML::sanitize($_POST['products_id']);
      $this->categoriesId = HTML::sanitize($_POST['categories_id']);
      $this->currentCategoryId = HTML::sanitize($_POST['current_category_id']);
      $this->copyAs = $_POST['copy_as'];

      $this->productsAdmin = new ProductsAdmin();
    }


    private function Link() {
      if ($this->categoriesId != $this->currentCategoryId) {
        $count = $this->productsAdmin->getCountProductsToCategory($this->ID, $this->categoriesId);

        if ($count < 1) {
          $sql_array = ['products_id' => $this->ID,
                        'categories_id' => $this->categoriesId
                       ];

          $this->app->db->save('products_to_categories', $sql_array);
        }
      }
    }

    private function productsDuplicate() {
      if ($this->copyAs == 'duplicate') {
         $this->productsAdmin->cloneProductsInOtherCategory($this->ID, $this->categoriesId);
      }
    }

    private function productsLink() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->copyAs == 'link') {
        if ($this->categoriesId != $this->currentCategoryId) {
          $this->Link();
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_cannot_link_to_same_category'), 'danger');
        }
      }
    }


    public function execute()  {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($this->ID) && isset($this->categoriesId)) {
        $this->productsDuplicate();
        $this->productsLink();
      }

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');

      $CLICSHOPPING_Hooks->call('Products','CopyConfirm');

      $this->app->redirect('Products&cPath=' . $this->categoriesId);
    }
  }