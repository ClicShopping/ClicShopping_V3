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

  namespace ClicShopping\Apps\Catalog\Products\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class StatsProductsAlert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
    }

    private function getProductsAlert() {

      $Qproducts = $this->app->db->prepare('select count(*) as count
                                            from :table_products
                                            where products_quantity <= :products_quantity
                                          ');
      $Qproducts->bindInt(':products_quantity', STOCK_REORDER_LEVEL);
      $Qproducts->execute();

      return $Qproducts->valueInt('count');
    }

    private function getProductsNotView() {

      $Qproducts = $this->app->db->prepare('select count(*) as count
                                            from :table_products
                                            where products_view = 0
                                          ');
      $Qproducts->execute();

      return $Qproducts->valueInt('count');
    }

    public function display() {
      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/stats_products_alert');

        $output = '
  <div class="card col-md-2 cardStatsWarning">
    <div class="card-block">
      <h4 class="StatsTitle">' . $this->app->getDef('text_products_alert_stock') . '</h4>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-md-left">
            <i class="fas fa-bell-slash fa-2x" aria-hidden="true"></i>
          </span>
          <span class="float-md-right">
            <div class="col-sm-12 StatsValue">' .  $this->getProductsAlert() . ' - ' . $this->app->getDef('text_products_alert_quantity') . '</div>
            <div class="col-sm-12 StatsValue">' .  $this->getProductsNotView() . ' - ' . $this->app->getDef('text_products_not_view') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
      ';

      return $output;
    }
  }