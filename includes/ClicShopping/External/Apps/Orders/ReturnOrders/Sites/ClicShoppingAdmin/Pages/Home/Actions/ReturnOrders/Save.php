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

use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Save extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');

    $return_id = HTML::sanitize($_POST['rId']);
    $return_status_id = HTML::sanitize($_POST['return_status']);

    if (isset($_POST['notify'])) {
      $notify = HTML::sanitize($_POST['notify']);
    } else {
      $notify = 0;
    }

    $comment = HTML::sanitize($_POST['comment']);

    $return_reason = HTML::sanitize($_POST['return_reason']);
    $return_action = HTML::sanitize($_POST['return_action']);
    $return_reason_opened = HTML::sanitize($_POST['return_reason_opened']);

    $sql_data_array = [
      'return_id' => $return_id,
      'return_status_id' => $return_status_id,
      'notify' => $notify,
      'comment' => $comment,
      'date_added' => 'now()',
      'admin_user_name' => AdministratorAdmin::getUserAdmin()
    ];

    $CLICSHOPPING_ReturnOrders->db->save('return_orders_history ', $sql_data_array);

    $Qupdate = $CLICSHOPPING_ReturnOrders->db->prepare('update :table_return_orders
                                                          set return_reason_id  = :return_reason,
                                                          return_action_id = :return_action,
                                                          opened = :return_reason_opened,    
                                                          date_modified = now()
                                                          where return_id = :return_id
                                                         ');
    $Qupdate->bindInt(':return_id', $return_id);
    $Qupdate->bindInt(':return_reason', $return_reason);
    $Qupdate->bindInt(':return_action', $return_action);
    $Qupdate->bindInt(':return_reason_opened', $return_reason_opened);
    $Qupdate->execute();


    $CLICSHOPPING_ReturnOrders->redirect('ReturnOrders&' . (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '' : ''));
  }
}