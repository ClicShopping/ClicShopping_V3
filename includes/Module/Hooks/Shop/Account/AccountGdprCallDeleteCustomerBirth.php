<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Account;

use ClicShopping\OM\Registry;

class AccountGdprCallDeleteCustomerBirth
{

  /**
   * Deletes the customer's date of birth from the database if the 'delete_customers_birth' POST parameter is set.
   *
   * @return void
   */
  public function execute()
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Customer = Registry::get('Customer');

    if (isset($_POST['delete_customers_birth'])) {
      $Qdelete = $CLICSHOPPING_Db->prepare('update :table_customers
                                              set customers_dob = null
                                              where customers_id = :customers_id
                                             ');
      $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qdelete->execute();
    }
  }
}
