<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\ReturnOrders\Sites\ClicShoppingAdmin\Pages\Home\Actions\ReturnOrders;

use ClicShopping\OM\Registry;

class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');

    if (isset($_POST['selected'])) {
      foreach ($_POST['selected'] as $id) {
        $Qdelete = $CLICSHOPPING_ReturnOrders->db->prepare('delete
                                                              from :table_return_orders 
                                                              where return_id = :return_id
                                                            ');
        $Qdelete->bindInt(':return_id', (int)$id);
        $Qdelete->execute();

        $Qdelete = $CLICSHOPPING_ReturnOrders->db->prepare('delete
                                                              from :table_return_orders_history
                                                              where return_id = :return_id
                                                            ');
        $Qdelete->bindInt(':return_id', (int)$id);
        $Qdelete->execute();
      }
    }

    $CLICSHOPPING_ReturnOrders->redirect('ReturnOrders', 'page=' . (int)$_GET['page']);
  }
}