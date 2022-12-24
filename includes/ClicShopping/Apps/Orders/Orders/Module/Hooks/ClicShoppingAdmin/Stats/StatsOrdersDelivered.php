<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Orders\Orders\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Orders\Orders\Orders as OrdersApp;

  class StatsOrdersDelivered implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {

      if (!Registry::exists('Orders')) {
        Registry::set('Orders', new OrdersApp());
      }

      $this->app = Registry::get('Orders');
    }

    private function statsOrderDelivered()
    {

      $QstatOrders = $this->app->db->prepare('select sum(ot.value) as value
                                              from :table_orders_total ot,
                                                   :table_orders o
                                              where ot.class = :class
                                              and o.orders_status = 3
                                              and o.orders_id = ot.orders_id
                                              and year(o.date_purchased) >= year(now())
                                             ');
      $QstatOrders->bindValue(':class', 'TO');
      $QstatOrders->execute();

      $statOrders = $QstatOrders->valueDecimal('value');

      return $statOrders;
    }

    private function statsOrderCancelled()
    {

      $QstatOrders = $this->app->db->prepare('select sum(ot.value) as value
                                              from :table_orders_total ot,
                                                   :table_orders o
                                              where ot.class = :class
                                              and o.orders_status = 4
                                              and o.orders_id = ot.orders_id
                                              and  year(o.date_purchased) >= year(now())
                                             ');
      $QstatOrders->bindValue(':class', 'TO');
      $QstatOrders->execute();

      $statOrders = $QstatOrders->valueDecimal('value');

      return $statOrders;
    }

    public function display()
    {
      if (!\defined('CLICSHOPPING_APP_ORDERS_OD_STATUS')) {
        return false;
      }

      $output = '';
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/stats_orders_turn_over');

      if ($this->statsOrderDelivered() > 0 || $this->statsOrderCancelled() > 0) {
        $output = '
<div class="col-md-2 col-12">
  <div class="card bg-danger">
    <div class="card-body">
      <h6 class="card-title text-white">' . $this->app->getDef('title_orders_turn_over') . '</h6>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-start">
            <i class="bi bi-cash text-white"></i>
          </span>
          <span class="float-end">
            <div class="text-white">' . $this->statsOrderDelivered() . ' - ' . $this->app->getDef('text_orders_delivered') . '</div>
            <div class="text-white">' . $this->statsOrderCancelled() . ' - ' . $this->app->getDef('text_orders_cancelled') . '</div>
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