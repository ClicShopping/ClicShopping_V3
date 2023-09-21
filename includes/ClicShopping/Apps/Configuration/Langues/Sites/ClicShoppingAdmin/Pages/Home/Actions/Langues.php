<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Langues\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Langues extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Langues = Registry::get('Langues');

    $this->page->setFile('langues.php');
    $this->page->data['action'] = 'Langues';

    $CLICSHOPPING_Langues->loadDefinitions('Sites/ClicShoppingAdmin/Langues');
  }
}