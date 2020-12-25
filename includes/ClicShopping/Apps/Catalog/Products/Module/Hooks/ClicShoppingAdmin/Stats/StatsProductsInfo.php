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

  namespace ClicShopping\Apps\Catalog\Products\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class StatsProductsInfo implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
    }

    private function getProductsArchive()
    {

      $Qproducts = $this->app->db->prepare('select count(products_id) as count
                                            from :table_products
                                            where products_archive = 1
                                          ');
      $Qproducts->execute();

      return $Qproducts->valueInt('count');
    }

    private function getNumberOfProducts()
    {
      $Qproducts = $this->app->db->prepare('select count(products_id) as count
                                            from :table_products
                                          ');
      $Qproducts->execute();

      return $Qproducts->valueInt('count');
    }

    public function display()
    {
      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/stats_products_info');

      $output = '
  <div class="card col-md-2 col-12 cardStatsPrimary">
    <div class="card-block">
      <h4 class="card-title StatsTitle">' . $this->app->getDef('text_products_info_title') . '</h4>
      <div class="card-text">
        <div class="col-sm-12 StatsValue">
          <span class="float-md-left">
            <i class="fas fa-asterisk fa-2x" aria-hidden="true"></i>
          </span>
          <span class="float-md-right">
            <div class="StatsValue">' . $this->getProductsArchive() . ' - ' . $this->app->getDef('text_products_info_archive') . '</div>
            <div class="StatsValue">' . $this->getNumberOfProducts() . ' - ' . $this->app->getDef('text_products_info_total') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
      ';

      return $output;
    }
  }