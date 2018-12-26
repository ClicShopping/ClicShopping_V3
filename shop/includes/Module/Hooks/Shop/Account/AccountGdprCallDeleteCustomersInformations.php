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

  namespace ClicShopping\OM\Module\Hooks\Shop\Account;

  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class AccountGdprCallDeleteCustomersInformations {

    public function execute() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if(isset($_POST['delete_customers_info'])) {
        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_customers_info
                                              set customers_info_date_of_last_logon = null,
                                                  customers_info_number_of_logons = 0,
                                                  customers_info_date_account_created = null,
                                                  customers_info_date_account_last_modified = null
                                              where customers_info_id = :customers_info_id
                                            ');
        $Qupdate->bindInt(':customers_info_id', $CLICSHOPPING_Customer->getID());
        $Qupdate->execute();
      }
    }
  }
