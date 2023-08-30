<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');

    $this->page->setFile('delete.php');
    $this->page->data['action'] = 'DeleteCategoryConfirm';

    $CLICSHOPPING_AdministratorMenu->loadDefinitions('Sites/ClicShoppingAdmin/AdministratorMenu');
  }
}