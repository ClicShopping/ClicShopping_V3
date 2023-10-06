<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\TotalTax\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\OrderTotal\TotalTax\TotalTax;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_TotalTax = new TotalTax();
    Registry::set('TotalTax', $CLICSHOPPING_TotalTax);

    $this->app = $CLICSHOPPING_TotalTax;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
