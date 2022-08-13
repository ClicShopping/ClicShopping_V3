<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\OrdersStatus\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class OrdersStatus extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_OrdersStatus = Registry::get('OrdersStatus');

      $this->page->setFile('orders_status.php');
      $this->page->data['action'] = 'OrdersStatus';

      $CLICSHOPPING_OrdersStatus->loadDefinitions('Sites/ClicShoppingAdmin/OrdersStatus');
    }
  }