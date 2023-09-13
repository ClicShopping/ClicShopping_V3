<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\ReviewsSentiment;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_POST['selected'])) {
      foreach ($_POST['selected'] as $id) {
        $Qid = $CLICSHOPPING_Reviews->db->get('reviews_sentiment', 'id', ['reviews_id' => (int)$id]);

        $CLICSHOPPING_Reviews->db->delete('reviews_sentiment', ['reviews_id' => (int)$id]);
        $CLICSHOPPING_Reviews->db->delete('reviews_sentiment_description', ['id' => (int)$Qid->valueInt('id')]);
      }
    }

    $CLICSHOPPING_Reviews->redirect('Reviews&page=' . $page);
  }
}