<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxRates\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\TaxRates\TaxRates;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_TaxRates = new TaxRates();
    Registry::set('TaxRates', $CLICSHOPPING_TaxRates);

    $this->app = $CLICSHOPPING_TaxRates;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
