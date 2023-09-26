<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Customers\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Create extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Customers = Registry::get('Customers');

    $this->page->setFile('create.php');
    $this->page->data['action'] = 'Create';

    $CLICSHOPPING_Customers->loadDefinitions('Sites/ClicShoppingAdmin/customers');
    $CLICSHOPPING_Customers->loadDefinitions('Sites/ClicShoppingAdmin/create');
  }
}