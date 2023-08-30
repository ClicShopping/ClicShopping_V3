<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Zones\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Zones\Zones;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Zones = new Zones();
    Registry::set('Zones', $CLICSHOPPING_Zones);

    $this->app = $CLICSHOPPING_Zones;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
