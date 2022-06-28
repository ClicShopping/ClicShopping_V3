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


  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class OrdersArchive extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Orders');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $orders_interval_date = 30;

      if (isset($_GET['OrdersArchive'])) {
        $QordersInfo = $this->app->db->prepare('select orders_id
                                                from :table_orders
                                                where last_modified > (CURDATE() - INTERVAL :orders_interval_date DAY)
                                                and orders_archive = 0
                                                and orders_status = 3
                                               ');
        $QordersInfo->bindInt('orders_interval_date', $orders_interval_date);
        $QordersInfo->execute();

        while ($QordersInfo->fetch()) {
          $update_array = [
            'orders_archive' => 1,
            'last_modified' => 'now()'
          ];

          $this->app->db->save('orders', $update_array, ['orders_id' => $QordersInfo->valueInt('orders_id') ]
          );
        }

        $this->app->redirect('Orders');
      } else {
        $CLICSHOPPING_MessageStack->add('text_errors_archive', 'warning');
        $this->app->redirect('Orders');
      }
    }
  }