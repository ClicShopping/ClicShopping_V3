<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


  namespace ClicShopping\Apps\Report\StatsProductsPurchased\Sites\ClicShoppingAdmin\Pages\Home\Actions\StatsProductsPurchased;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_StatsProductsPurchased = Registry::get('StatsProductsPurchased');

      if (isset($_GET['resetPurchased'])) $resetPurchased = HTML::sanitize($_GET['resetPurchased']);
      if (isset($_GET['products_id'])) $products_id = HTML::sanitize($_GET['products_id']);

      if ( $resetPurchased == '0' ) {
        $Qupdate = $CLICSHOPPING_StatsProductsPurchased->db->prepare('update :table_products
                                                                set products_ordered = 0
                                                                where 1
                                                              ');
        $Qupdate->execute();
      } else {
        // Reset selected product count
        $Qupdate = $CLICSHOPPING_StatsProductsPurchased->db->prepare('update :table_products
                                                                set products_ordered = 0
                                                                where products_id = :products_id
                                                              ');
        $Qupdate->bindInt(':products_id', (int)$products_id);
        $Qupdate->execute();
      }
    }
  }
