<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions\Featured;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Insert extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {

    $CLICSHOPPING_Featured = Registry::get('Featured');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    $products_id = HTML::sanitize($_POST['products_id']);

    if (!empty($_POST['expdate'])) {
      $expdate = HTML::sanitize($_POST['expdate']);
    } else {
      $expdate = null;
    }

    if (!empty($_POST['expdate'])) {
      $schdate = HTML::sanitize($_POST['schdate']);
    } else {
      $schdate = null;
    }

    $CLICSHOPPING_Featured->db->save('products_featured', [
        'products_id' => (int)$products_id,
        'products_featured_date_added' => 'now()',
        'scheduled_date' => $schdate,
        'expires_date' => $expdate,
        'status' => 1
      ]
    );

    $CLICSHOPPING_Hooks->call('Featured', 'Insert');

    $CLICSHOPPING_Featured->redirect('Featured', 'page=' . $page);
  }
}