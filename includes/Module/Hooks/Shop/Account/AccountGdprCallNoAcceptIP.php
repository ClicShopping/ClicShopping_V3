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

  class AccountGdprCallNoAcceptIP
  {

    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qcheck = $CLICSHOPPING_Db->prepare('select customers_id,
                                                  no_ip_address
                                           from :table_customers_gdpr
                                           where customers_id = :customers_id
                                         ');
      $Qcheck->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qcheck->execute();

      if ($Qcheck->fetch() === false) {
        $CLICSHOPPING_Db->save('customers_gdpr', ['customers_id' => $CLICSHOPPING_Customer->getID()]);
      } else {
        if (!\is_null($_POST['no_ip_address'])) {
          $no_ip_address = 1;
        } else {
          $no_ip_address = 0;
        }

        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_customers_gdpr
                                              set no_ip_address = :no_ip_address,
                                              customers_id = :customers_id
                                            ');
        $Qupdate->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qupdate->bindInt(':no_ip_address', $no_ip_address);

        $Qupdate->execute();
      }
    }
  }
