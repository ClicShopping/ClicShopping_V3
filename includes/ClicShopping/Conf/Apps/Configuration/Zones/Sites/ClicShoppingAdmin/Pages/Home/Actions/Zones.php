<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Zones\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Zones extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Zones = Registry::get('Zones');

    $this->page->setFile('zones.php');
    $this->page->data['action'] = 'Zones';

    $CLICSHOPPING_Zones->loadDefinitions('Sites/ClicShoppingAdmin/Zones');
  }
}