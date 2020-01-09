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

  class dashboardProductsExpected implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/TopDashboard/dashboard_products_expected');
    }

    public function Display(): string
    {
      $Qproducts = $this->app->db->prepare('select count(*) as count 
                                            from :table_products 
                                            where products_date_available <> null
                                            ');
      $Qproducts->execute();

      $number_products_expected = $Qproducts->valueInt('count');

      $output = '';

      if ($number_products_expected != 0) {
        $text = $this->app->getDef('text_number_products_expected');
        $text_view = $this->app->getDef('text_view');

        $output = '
<span style="padding-right:0.5rem; padding-top:0.5rem">
  <div class="card bg-primary">
    <div class="card-body">
      <div class="row">
        <h5 class="card-title text-white"><i class="fas fas fa-info"  aria-hidden="true"></i> ' . $text . '</h5>
      </div>
      <div class="col-md-12">
        <span class="text-white"><strong>' . $number_products_expected . '</strong></span>
        <span><small class="text-white">' . HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Products&StatsProductsExpected'), $text_view, 'class="text-white"') . '</small></span>
      </div>
    </div>
  </div>
</span>
';
      }

      return $output;
    }
  }