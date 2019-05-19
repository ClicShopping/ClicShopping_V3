<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Report\StatsProductsViewed\Sites\ClicShoppingAdmin\Pages\Home\Actions\StatsProductsViewed;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_StatsProductsViewed = Registry::get('StatsProductsViewed');

      if (isset($_GET['resetViewed'])) $resetViewed = HTML::sanitize($_GET['resetViewed']);
      if (isset($_GET['products_id'])) $products_id = HTML::sanitize($_GET['products_id']);

      if ($resetViewed == '0') {
// Reset ALL counts
        $Qupdate = $CLICSHOPPING_StatsProductsViewed->db->prepare('update :table_products_description
                                                            set products_viewed = 0
                                                            where 1
                                                          ');
        $Qupdate->execute();

      } else {
// Reset selected product count
        $Qupdate = $CLICSHOPPING_StatsProductsViewed->db->prepare('update :table_products_description
                                                            set products_viewed = 0
                                                            where products_id = :products_id
                                                          ');
        $Qupdate->bindInt(':products_id', (int)$products_id);
        $Qupdate->execute();
      }


    }
  }
