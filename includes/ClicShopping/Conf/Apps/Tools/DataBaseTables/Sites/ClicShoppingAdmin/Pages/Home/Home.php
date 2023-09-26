<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\DataBaseTables\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\DataBaseTables\DataBaseTables;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_DataBaseTables = new DataBaseTables();
    Registry::set('DataBaseTables', $CLICSHOPPING_DataBaseTables);

    $this->app = Registry::get('DataBaseTables');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
