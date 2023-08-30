<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\Cronjob\Cronjob;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_Cronjob = new Cronjob();
    Registry::set('Cronjob', $CLICSHOPPING_Cronjob);

    $this->app = $CLICSHOPPING_Cronjob;

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
