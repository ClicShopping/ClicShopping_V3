<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\MoneyOrder\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Payment\MoneyOrder\MoneyOrder;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_MoneyOrder = new MoneyOrder();
    Registry::set('MoneyOrder', $CLICSHOPPING_MoneyOrder);

    $this->app = $CLICSHOPPING_MoneyOrder;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
