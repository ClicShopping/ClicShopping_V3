<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\ContactSuccess;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Communication\PageManager\PageManager as PageManagerApp;

class ContactSuccess extends \ClicShopping\OM\PagesAbstract
{
  public mixed $app;

  protected function init()
  {
    if (!Registry::exists('PageManager')) {
      Registry::set('PageManager', new PageManagerApp());
    }

    $CLICSHOPPING_PageManager = Registry::get('PageManager');

    $CLICSHOPPING_PageManager->loadDefinitions('Sites/Shop/main');
  }
}
