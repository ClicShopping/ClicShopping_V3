<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Orders\ReturnOrders\Sites\Shop\Pages\ProductReturn;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Orders\ReturnOrders\ReturnOrders as ReturnOrdersApp;

  class ProductReturn extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      if (!Registry::exists('ReturnOrders')) {
        Registry::set('ReturnOrders', new ReturnOrdersApp());
      }

      $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');

      $CLICSHOPPING_ReturnOrders->loadDefinitions('Sites/Shop/main');
    }
  }
