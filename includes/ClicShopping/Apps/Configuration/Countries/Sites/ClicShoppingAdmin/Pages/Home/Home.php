<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Countries\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Countries\Countries;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Countries = new Countries();
    Registry::set('Countries', $CLICSHOPPING_Countries);

    $this->app = $CLICSHOPPING_Countries;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
