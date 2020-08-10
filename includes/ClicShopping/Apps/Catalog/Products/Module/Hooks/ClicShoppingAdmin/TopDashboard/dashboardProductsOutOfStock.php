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

  namespace ClicShopping\Apps\Catalog\Products\Module\Hooks\ClicShoppingAdmin\TopDashboard;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class dashboardProductsOutOfStock implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/TopDashboard/dashboard_products_out_of_stock');
    }

    public function Display(): string
    {
      $Qproducts =  $this->app->db->prepare('select count(products_id) as count 
                                             from :table_products 
                                             where products_quantity = 0
                                          ');

      $Qproducts->execute();

      $number_products_out_of_stock = $Qproducts->valueInt('count');
      $output = '';

      if ($number_products_out_of_stock > 0 && STOCK_CHECK == 'true') {
        $text = $this->app->getDef('text_number_products_out_of_stock');
        $text_view = $this->app->getDef('text_view');

        $output = '
<span style="padding-right:0.5rem; padding-top:0.5rem">
  <div class="card bg-danger">
    <div class="card-body">
      <div class="row">
        <h5 class="card-title text-white"><i class="fas fa-fire"  aria-hidden="true"></i> ' . $text . '</h5>
      </div>
      <div class="col-md-12">
        <span class="text-white"><strong>' . $number_products_out_of_stock . '</strong></span>
        <span><small class="text-white">' . HTML::link(CLICSHOPPING::link(null, 'A&Catalog\\Products&StatsProductsLowStock'), $text_view, 'class="text-white"') . '</small></span>
      </div>
    </div>
  </div>
</span>
';
      }

      return $output;
    }
  }