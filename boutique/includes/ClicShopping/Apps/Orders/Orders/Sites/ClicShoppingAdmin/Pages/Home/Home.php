<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Orders\Orders\Orders;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_Orders = new Orders();
      Registry::set('Orders', $CLICSHOPPING_Orders);

      $this->app = Registry::get('Orders');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
