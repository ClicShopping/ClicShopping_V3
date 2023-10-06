<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\Reviews;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class DeleteAllVote extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_POST['selected'])) {
      foreach ($_POST['selected'] as $id) {
        $reviews_id = HTML::sanitize($id);

        $CLICSHOPPING_Reviews->db->delete('reviews', ['reviews_id' => (int)$reviews_id]);
      }
    }

    $CLICSHOPPING_Hooks->call('Reviews', 'DeleteAllVote');

    $CLICSHOPPING_Reviews->redirect('StatsCustomersVote&page=' . $page);
  }
}