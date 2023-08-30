<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Sites\ClicShoppingAdmin\Pages\Home\Actions\ChatGpt;

use ClicShopping\OM\Registry;

class DeleteEverything extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_ChatGpt = Registry::get('ChatGpt');

    if (isset($_GET['ChatGpt']) && isset($_GET['DeleteEverything'])) {
      $CLICSHOPPING_ChatGpt->db->delete('gpt');
    }

    $CLICSHOPPING_ChatGpt->redirect('ChatGpt');
  }
}