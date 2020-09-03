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

  namespace ClicShopping\Apps\Configuration\OrdersStatus\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\OrdersStatus\OrdersStatus;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_OrdersStatus = new OrdersStatus();
      Registry::set('OrdersStatus', $CLICSHOPPING_OrdersStatus);

      $this->app = $CLICSHOPPING_OrdersStatus;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
