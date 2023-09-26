<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\Apps\Catalog\Manufacturers\Manufacturers;
use ClicShopping\OM\Registry;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Manufacturers = new Manufacturers();
    Registry::set('Manufacturers', $CLICSHOPPING_Manufacturers);

    $this->app = Registry::get('Manufacturers');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
