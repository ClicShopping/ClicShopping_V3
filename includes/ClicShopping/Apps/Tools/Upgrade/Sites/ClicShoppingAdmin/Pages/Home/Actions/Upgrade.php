<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Upgrade extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Upgrade = Registry::get('Upgrade');

    $this->page->setFile('upgrade.php');
    $this->page->data['action'] = 'Upgrade';

    $CLICSHOPPING_Upgrade->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}