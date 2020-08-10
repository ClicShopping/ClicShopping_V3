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


  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions\Orders;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class ArchiveBatch extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Orders');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $orders_id_start = HTML::sanitize($_POST['orders_id_start']);
      $orders_id_end = HTML::sanitize($_POST['orders_id_end']);

      if (!empty($orders_id_start) && !empty($orders_id_end)) {
        if ($orders_id_start != 0 && $orders_id_end != 0) {
          if ($orders_id_start > $orders_id_end) {
            $CLICSHOPPING_MessageStack->add('text_errors_order', 'warning');
            $this->app->redirect('Orders');
          }

          $between_orders_id = 'orders_id between ' . $orders_id_start . ' and ' . $orders_id_end;
        }

        $QordersInfo = $this->app->db->prepare('select orders_id
                                                from :table_orders
                                                where ' . $between_orders_id . '
                                               ');

        $QordersInfo->execute();

        while ($QordersInfo->fetch()) {
          $this->app->db->save('orders', ['orders_archive' => 1,
            'last_modified' => 'now()'
          ], [
              'orders_id' => $QordersInfo->valueInt('orders_id')
            ]
          );
        }

        $this->app->redirect('Orders');
      } else {
        $CLICSHOPPING_MessageStack->add('text_errors_archive', 'warning');
      }
    }
  }