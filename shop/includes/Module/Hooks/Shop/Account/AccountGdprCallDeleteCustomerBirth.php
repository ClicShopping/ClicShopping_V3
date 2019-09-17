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

  namespace ClicShopping\OM\Module\Hooks\Shop\Account;

  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class AccountGdprCallDeleteCustomerBirth
  {

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
