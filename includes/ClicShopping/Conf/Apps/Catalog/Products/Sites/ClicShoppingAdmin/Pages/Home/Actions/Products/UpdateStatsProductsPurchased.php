<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class UpdateStatsProductsPurchased extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {
    $CLICSHOPPING_Products = Registry::get('Products');

    if (isset($_GET['resetPurchased'])) {
      $resetPurchased = HTML::sanitize($_GET['resetPurchased']);

      if (isset($_GET['products_id'])) $products_id = HTML::sanitize($_GET['products_id']);

      if ($resetPurchased == '0') {
        $Qupdate = $CLICSHOPPING_Products->db->prepare('update :table_products
                                                          set products_ordered = 0
                                                          where 1
                                                        ');
        $Qupdate->execute();
      } else {
        // Reset selected product count
        $Qupdate = $CLICSHOPPING_Products->db->prepare('update :table_products
                                                          set products_ordered = 0
                                                          where products_id = :products_id
                                                        ');
        $Qupdate->bindInt(':products_id', $products_id);
        $Qupdate->execute();
      }
    }

    $CLICSHOPPING_Products->redirect('StatsProductsPurchased');
  }
}
