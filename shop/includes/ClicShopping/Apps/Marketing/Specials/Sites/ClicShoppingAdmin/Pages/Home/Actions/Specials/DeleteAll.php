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


  namespace ClicShopping\Apps\Marketing\Specials\Sites\ClicShoppingAdmin\Pages\Home\Actions\Specials;

  use ClicShopping\OM\Registry;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Specials = Registry::get('Specials');

      if (!empty($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {

          $Qdelete = $CLICSHOPPING_Specials->db->prepare('delete
                                                    from :table_specials
                                                    where specials_id = :specials_id
                                                  ');
          $Qdelete->bindInt(':specials_id', (int)$id);
          $Qdelete->execute();
        }
      }

      $CLICSHOPPING_Specials->redirect('Specials', 'page=' . $_GET['page']);
    }
  }