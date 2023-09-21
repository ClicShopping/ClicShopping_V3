<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Orders\Orders\Module\Hooks\ClicShoppingAdmin\Stats;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Orders\Orders\Orders as OrdersApp;

class StatsOrdersStatus implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Orders')) {
      Registry::set('Orders', new OrdersApp());
    }

    $this->app = Registry::get('Orders');
  }

  /**
   * @return int
   */
  private function statsOrderStatusPending(): int
  {
    $QstatOrders = $this->app->db->prepare('select count(*) as count
                                              from :table_orders
                                              where orders_status = 1
                                             ');
    $QstatOrders->execute();

    $statOrders = $QstatOrders->valueInt('count');

    return $statOrders;
  }

  /**
   * @return int
   */
  private function statsOrderStatusProcessing(): int
  {
    $QstatOrders = $this->app->db->prepare('select count(*) as count
                                              from :table_orders
                                              where orders_status = 2
                                             ');
    $QstatOrders->execute();

    $statOrders = $QstatOrders->valueInt('count');

    return $statOrders;
  }

  public function display(): string
  {
    if (!\defined('CLICSHOPPING_APP_ORDERS_OD_STATUS')) {
      return false;
    }

    $output = '';
    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/stats_orders_status');

    if ($this->statsOrderStatusPending() > 0 || $this->statsOrderStatusProcessing() > 0) {
      $output = '
<div class="col-md-2 col-12">
  <div class="card bg-warning">
    <div class="card-body">
      <h6 class="card-title text-white">' . $this->app->getDef('title_orders_customers_status') . '</h6>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-start">
            <i class="bi bi-info-circle text-white"></i>
          </span>
          <span class="float-end">
            <div class="text-white">' . $this->statsOrderStatusPending() . ' - ' . $this->app->getDef('text_orders_pending') . '</div>
            <div class="text-white">' . $this->statsOrderStatusProcessing() . ' - ' . $this->app->getDef('text_orders_processing') . '</div>            
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