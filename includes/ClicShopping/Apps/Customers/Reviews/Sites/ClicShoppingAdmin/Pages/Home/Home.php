<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\Apps\Customers\Reviews\Reviews;
use ClicShopping\OM\Registry;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Reviews = new Reviews();
    Registry::set('Reviews', $CLICSHOPPING_Reviews);

    $this->app = Registry::get('Reviews');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
