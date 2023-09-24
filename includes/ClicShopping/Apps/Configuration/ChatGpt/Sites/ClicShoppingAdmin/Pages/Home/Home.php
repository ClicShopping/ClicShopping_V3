<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_ChatGpt = new ChatGpt();
    Registry::set('ChatGpt', $CLICSHOPPING_ChatGpt);

    $this->app = $CLICSHOPPING_ChatGpt;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
