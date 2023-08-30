<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Favorites\Sites\Shop\Pages\Favorites;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

class Favorites extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    if (!Registry::exists('Favorites')) {
      Registry::set('Favorites', new FavoritesApp());
    }

    $CLICSHOPPING_Favorites = Registry::get('Favorites');

    $CLICSHOPPING_Favorites->loadDefinitions('Sites/Shop/main');
  }
}
