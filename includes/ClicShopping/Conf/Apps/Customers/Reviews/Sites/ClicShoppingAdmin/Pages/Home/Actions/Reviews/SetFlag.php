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

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Reviews\Classes\ClicShoppingAdmin\ReviewsAdmin;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_GET['id'])) {
      ReviewsAdmin::getReviewsStatus((int)$_GET['id'], (int)$_GET['flag']);
    }

    $CLICSHOPPING_Reviews->redirect('Reviews&Reviews&page=' . $page . '&rID=' . (int)$_GET['id']);
  }
}