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

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsStatusAdmin;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct(){
      $this->app = Registry::get('Products');
    }

    public function execute()  {
      if ( ($_GET['flag'] == 0) || ($_GET['flag'] == 1) ) {
        if (isset($_GET['pID'])) {
          ProductsStatusAdmin::getProductStatus($_GET['pID'], $_GET['flag']);
        }

        Cache::clear('categories');
        Cache::clear('products-also_purchased');
        Cache::clear('products_related');
        Cache::clear('products_cross_sell');
        Cache::clear('upcoming');
      }

      $this->app->redirect('Products&cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID']);
    }
  }