<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Move extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');

    $this->page->setFile('move.php');
    $this->page->data['action'] = 'MoveCategoryConfirm';

    $CLICSHOPPING_AdministratorMenu->loadDefinitions('Sites/ClicShoppingAdmin/AdministratorMenu');
  }
}