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

  namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions\Featured;

  use ClicShopping\OM\Registry;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Featured = Registry::get('Featured');

      if (isset($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {

          $Qdelete = $CLICSHOPPING_Featured->db->prepare('delete
                                            from :table_products_featured
                                            where products_featured_id = :products_featured_id
                                          ');
          $Qdelete->bindInt(':products_featured_id', (int)$id);
          $Qdelete->execute();
        }
      }

      $CLICSHOPPING_Featured->redirect('Featured', 'page=' . $_GET['page']);
    }
  }