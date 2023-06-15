<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Products\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class StatsProductsOutOfStock implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
    }

    private function getOutOfStock()
    {

      $Qproducts = $this->app->db->prepare('select count(products_id) as count
                                            from :table_products
                                            where products_quantity <= 0
                                          ');
      $Qproducts->execute();

      return $Qproducts->valueInt('count');
    }

    private function getProductsOffLine()
    {

      $Qproducts = $this->app->db->prepare('select count(products_id) as count
                                            from :table_products
                                            where products_status = 0
                                          ');
      $Qproducts->execute();

      return $Qproducts->valueInt('count');
    }

    public function display()
    {
      if (!\defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/stats_products_out_of_stock');

      if ($this->getOutOfStock() == 0 && $this->getProductsOffLine() == 0) {
        $output = '';
      } else {
        $output = '
 <div class="col-md-2 col-12">
    <div class="card bg-danger">
     <div class="card-body">
      <h6 class="card-title text-white">' . $this->app->getDef('text_products_stock') . '</h6>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-start">
            <i class="bi bi-cone-striped text-white"></i>
          </span>
          <span class="float-end">
              <div class="col-sm-12 text-white">' . $this->getOutOfStock() . ' - ' . $this->app->getDef('text_products_out_of_stock') . '</div>
              <div class="col-sm-12 text-white">' . $this->getProductsOffLine() . ' - ' . $this->app->getDef('text_products_offline') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>  
      ';
      }

      return $output;
    }
  }