<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\Apps\Communication\PageManager\PageManager;
use ClicShopping\OM\Registry;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_PageManager = new PageManager();
    Registry::set('PageManager', $CLICSHOPPING_PageManager);

    $this->app = Registry::get('PageManager');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
