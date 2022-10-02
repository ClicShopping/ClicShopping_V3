<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Orders\ReturnOrders\Module\Hooks\ClicShoppingAdmin\TopDashboard;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Orders\ReturnOrders\ReturnOrders as ReturnOrdersApp;

  class DashboardReturnOrders implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('ReturnOrders')) {
        Registry::set('ReturnOrders', new ReturnOrdersApp());
      }

      $this->app = Registry::get('ReturnOrders');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/TopDashboard/dashboard_top_return_order');
    }

    public function Display(): string
    {
      $Qreturn = $this->app->db->prepare('select count(return_id) as count 
                                          from :table_return_orders
                                          where opened = 0
                                         ');
      $Qreturn->execute();

      $number_return = $Qreturn->valueInt('count');
      $output = '';

      if ($number_return > 0) {
        $text = $this->app->getDef('text_newsletter');
        $text_view = $this->app->getDef('text_view');

        $output = '
<div class="col-md-2 col-12 m-1">
    <div class="card bg-warning">
      <div class="card-body">
        <div class="row">
          <h6 class="card-title text-white"><i class="bi bi-bell-fill"></i> ' . $text . '</h6>
        </div>
        <div class="col-md-12">
          <span class="text-white"><strong>' . $number_return . '</strong></span>
          <span><small class="text-white">' . HTML::link(CLICSHOPPING::link(null, 'A&Orders\ReturnOrders&ReturnOrders'), $text_view, 'class="text-white"') . '</small></span>
        </div>
      </div>
    </div>
</div>
';
      }

      return $output;
    }
  }