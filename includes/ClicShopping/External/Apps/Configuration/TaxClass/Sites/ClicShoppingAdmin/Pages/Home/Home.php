<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxClass\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\TaxClass\TaxClass;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_TaxClass = new TaxClass();
    Registry::set('TaxClass', $CLICSHOPPING_TaxClass);

    $this->app = $CLICSHOPPING_TaxClass;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
