<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\Newsletter\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\Apps\Communication\Newsletter\Newsletter;
use ClicShopping\OM\Registry;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Newsletter = new Newsletter();
    Registry::set('Newsletter', $CLICSHOPPING_Newsletter);

    $this->app = Registry::get('Newsletter');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
