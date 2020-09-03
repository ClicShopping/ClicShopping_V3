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

  namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\OrdersStatusInvoice\OrdersStatusInvoice;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_OrdersStatusInvoice = new OrdersStatusInvoice();
      Registry::set('OrdersStatusInvoice', $CLICSHOPPING_OrdersStatusInvoice);

      $this->app = $CLICSHOPPING_OrdersStatusInvoice;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
