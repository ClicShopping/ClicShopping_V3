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

  class AccountGdprCallDeleteAccount
  {

    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_POST['delete_customers_account_checkbox']) && is_numeric($_POST['delete_customers_account_checkbox'])) {
        $delete_customers_account_checkbox = HTML::sanitize($_POST['delete_customers_account_checkbox']);
      } else {
        $delete_customers_account_checkbox = '0';
      }

      if ($delete_customers_account_checkbox == '1') {
        $process = false;

        $Qcheck = $CLICSHOPPING_Db->prepare('select count(orders_status) as count
                                              from :table_orders
                                              where customers_id = :customers_id
                                              and orders_status <> 3
                                            ');
        $Qcheck->bindValue(':customers_id', $CLICSHOPPING_Customer->getID());
        $Qcheck->execute();

        if ($Qcheck->valueInt('count') != 0) {
          $process = true;
        }

        if ($process === false) {
          $QcustomerEmail = $CLICSHOPPING_Db->prepare('select customers_email_address,
                                                              customers_firstname,
                                                              customers_lastname
                                                       from :table_customers
                                                       where customers_id = :customers_id
                                                      ');
          $QcustomerEmail->bindValue(':customers_id', $CLICSHOPPING_Customer->getID());
          $QcustomerEmail->execute();

          $text_email = html_entity_decode(CLICSHOPPING::getDef('module_account_customers_gdpr_email_text_message')) . "\n";

          $CLICSHOPPING_Mail->clicMail($QcustomerEmail->value('customers_firstname') . ' ' . $QcustomerEmail->value('customers_lastname'), $QcustomerEmail->value('customers_email_address'), CLICSHOPPING::getDef('email_text_subject'), $text_email, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);

          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                from :table_customers
                                                where customers_id = :customers_id
                                              ');
          $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qdelete->execute();

          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                from :table_customers_basket
                                                where customers_id = :customers_id
                                              ');
          $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qdelete->execute();


          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                            from :table_customers_basket_attributes
                                            where customers_id = :customers_id
                                          ');
          $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qdelete->execute();


          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                from :table_customers_info
                                                where customers_info_id = :customers_id
                                              ');
          $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qdelete->execute();


          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                from :table_address_book
                                                where customers_id = :customers_id
                                              ');
          $Qdelete->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qdelete->execute();

          $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                from :table_action_recorder
                                                where user_name = :user_name
                                              ');
          $Qdelete->bindValue(':user_name', $CLICSHOPPING_Customer->getEmailAddress());
          $Qdelete->execute();

          $Qcheck = $CLICSHOPPING_Db->prepare('select reviews_id
                                              from :table_reviews
                                              where customers_id = :customers_id
                                             ');
          $Qcheck->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qcheck->execute();

          while ($Qcheck->fetch()) {
            $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                   from :table_reviews
                                                   where customers_id = :customers_id
                                                 ');
            $Qdelete->bindInt(':reviews_id', $Qcheck->valueInt('reviews_id'));
            $Qdelete->execute();

            $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                   from :table_reviews_description
                                                   where reviews_id = :reviews_id
                                                ');
            $Qdelete->bindInt(':reviews_id', $Qcheck->valueInt('reviews_id'));
            $Qdelete->execute();
          }

          $CLICSHOPPING_Customer->reset();
          $CLICSHOPPING_ShoppingCart->reset();

          CLICSHOPPING::redirect();

        } else {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('module_account_customers_gdpr_text_error_delete'), 'error');
          CLICSHOPPING::redirect(null, 'Account&Delete');
        }
      }
    }
  }
