<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class ChatGpt extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Currency = Registry::get('ChatGpt');

    $this->page->setFile('chatgpt.php');
    $this->page->data['action'] = 'ChatGpt';

    $CLICSHOPPING_Currency->loadDefinitions('Sites/ClicShoppingAdmin/main');
  }
}