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

  namespace ClicShopping\Apps\Catalog\Archive\Sites\ClicShoppingAdmin\Pages\Home\Actions\Archive;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Archive = Registry::get('Archive');

      if (isset($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {
          $Qupdate = $CLICSHOPPING_Archive->db->prepare('update :table_products
                                                  set products_archive = :products_archive
                                                  where products_id = :products_id
                                                ');
          $Qupdate->bindInt(':products_archive', '0');
          $Qupdate->bindInt(':products_id', $id);
          $Qupdate->execute();
        }
      }

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');

      $CLICSHOPPING_Archive->redirect('Archive');
    }
  }