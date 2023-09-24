<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Archive\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Archive\Archive;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Archive = new Archive();
    Registry::set('Archive', $CLICSHOPPING_Archive);

    $this->app = Registry::get('Archive');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
