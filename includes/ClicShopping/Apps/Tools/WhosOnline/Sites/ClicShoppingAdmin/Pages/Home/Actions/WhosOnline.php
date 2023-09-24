<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\WhosOnline\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class WhosOnline extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_WhosOnline = Registry::get('WhosOnline');

    $this->page->setFile('whos_online.php');

    $CLICSHOPPING_WhosOnline->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}