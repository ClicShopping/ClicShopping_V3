<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Currency\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Currency\Currency;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Currency = new Currency();
    Registry::set('Currency', $CLICSHOPPING_Currency);

    $this->app = $CLICSHOPPING_Currency;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
