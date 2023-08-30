<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Categories\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\Apps\Catalog\Categories\Categories;
use ClicShopping\OM\Registry;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Categories = new Categories();
    Registry::set('Categories', $CLICSHOPPING_Categories);

    $this->app = Registry::get('Categories');
    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
