<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Administrators\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Administrators extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Administrators = Registry::get('Administrators');

    $this->page->setFile('administrators.php');
    $this->page->data['action'] = 'Administrators';

    $CLICSHOPPING_Administrators->loadDefinitions('Sites/ClicShoppingAdmin/Administrators');
  }
}