<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditDesign\Sites\ClicShoppingAdmin\Pages\Home;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\EditDesign\EditDesign;

class Home extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    $CLICSHOPPING_EditDesign = new EditDesign();
    Registry::set('EditDesign', $CLICSHOPPING_EditDesign);

    $this->app = Registry::get('EditDesign');

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}
