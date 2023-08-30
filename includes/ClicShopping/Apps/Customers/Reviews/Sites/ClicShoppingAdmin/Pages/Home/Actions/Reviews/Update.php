<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\Reviews;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Update extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_GET['rID'])) {
      $reviews_id = HTML::sanitize($_GET['rID']);

      if (isset($_POST['reviews_text'])) {
        $reviews_text = HTML::sanitize($_POST['reviews_text']);
      }

      if (isset($_POST['status'])) {
        $reviews_status = HTML::sanitize($_POST['status']);
      }

      if (isset($_POST['languages_id'])) {
        $languages_id = HTML::sanitize($_POST['languages_id']);
      }

      $sql_array = [
        'status' => (int)$reviews_status,
        'last_modified' => 'now()'
      ];

      $CLICSHOPPING_Reviews->db->save('reviews', $sql_array, ['reviews_id' => (int)$reviews_id]);

      $sql_array = [
        'reviews_text' => $reviews_text,
        'languages_id' => (int)$languages_id,
      ];

      $CLICSHOPPING_Reviews->db->save('reviews_description', $sql_array, ['reviews_id' => (int)$reviews_id]);

      $CLICSHOPPING_Reviews->redirect('Reviews&page=' . $page);
    }
  }
}