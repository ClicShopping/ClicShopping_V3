<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class SelectPage extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_PageManager = Registry::get('PageManager');

    $this->page->setFile('select_page.php');
    $this->page->data['action'] = 'select_page';

    $CLICSHOPPING_PageManager->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}