<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Archive\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Archive extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Archive = Registry::get('Archive');

    $this->page->setFile('archive.php');
    $this->page->data['action'] = 'Archive';

    $CLICSHOPPING_Archive->loadDefinitions('Sites/ClicShoppingAdmin/Archive');
  }
}