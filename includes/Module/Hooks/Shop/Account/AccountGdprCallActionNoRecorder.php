<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Account;

use ClicShopping\OM\Registry;

class AccountGdprCallActionNoRecorder
{

  public function execute()
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Customer = Registry::get('Customer');

    if (isset($_POST['action_recorder'])) {
      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                             from :table_action_recorder
                                             where user_name = :user_name
                                            ');
      $Qdelete->bindValue(':user_name', $CLICSHOPPING_Customer->getEmailAddress());
      $Qdelete->execute();
    }
  }
}
