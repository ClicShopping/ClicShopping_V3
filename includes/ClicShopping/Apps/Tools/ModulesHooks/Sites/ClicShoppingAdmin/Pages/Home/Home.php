<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\ModulesHooks\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\ModulesHooks\ModulesHooks;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_ModulesHooks = new ModulesHooks();
    Registry::set('ModulesHooks', $CLICSHOPPING_ModulesHooks);

    $this->app = Registry::get('ModulesHooks');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
