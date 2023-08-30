<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\ReturnOrders\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Orders\ReturnOrders\ReturnOrders;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_ReturnOrders = new ReturnOrders();
    Registry::set('ReturnOrders', $CLICSHOPPING_ReturnOrders);

    $this->app = Registry::get('ReturnOrders');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
