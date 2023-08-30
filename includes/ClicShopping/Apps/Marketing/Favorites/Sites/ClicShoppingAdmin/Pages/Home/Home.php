<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Favorites\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Favorites\Favorites;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Favorites = new Favorites();
    Registry::set('Favorites', $CLICSHOPPING_Favorites);

    $this->app = $CLICSHOPPING_Favorites;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
