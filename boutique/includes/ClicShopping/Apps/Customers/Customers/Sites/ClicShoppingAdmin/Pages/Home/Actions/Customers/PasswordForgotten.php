<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */


  namespace ClicShopping\Apps\Customers\Customers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Customers;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Hash;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  class PasswordForgotten extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Customer = Registry::get('Customers');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Mail = Registry::get('Mails');

      $CLICSHOPPING_Customer->loadDefinitions('Sites/ClicShoppingAdmin/password_forgotten');

      $QcheckCustomer = $CLICSHOPPING_Customer->db->prepare('select customers_firstname,
                                                                     customers_lastname,
                                                                     customers_password,
                                                                     customers_id,
                                                                     customers_email_address
                                                               from :table_customers
                                                               where customers_id = :customers_id
                                                             ');
      $QcheckCustomer->bindInt(':customers_id', (int)$_GET['cID'] );
      $QcheckCustomer->execute();

      if (!empty($QcheckCustomer->value('customers_email_address'))) {
// Crypted password mods - create a new password, update the database and mail it to them
        $newpass = Hash::getRandomString(ENTRY_PASSWORD_MIN_LENGTH);
        $crypted_password = Hash::encrypt($newpass);

        $Qupdate = $CLICSHOPPING_Customer->db->prepare('update :table_customers
                                                          set customers_password = :customers_password
                                                          where customers_id = :customers_id
                                                        ');
        $Qupdate->bindValue(':customers_password', $crypted_password);
        $Qupdate->bindInt(':customers_id', (int)$QcheckCustomer->valueInt('customers_id'));
        $Qupdate->execute();

        $text_password_body = $CLICSHOPPING_Customer->getDef('email_password_reminder_body', ['username' => $QcheckCustomer->value('customers_email_address'),
                                                                                               'store_name' => STORE_NAME,
                                                                                               'password' => $newpass,
                                                                                               'store_name_address' => STORE_NAME_ADDRESS,
                                                                                               'store_owner_email_address' => STORE_OWNER_EMAIL_ADDRESS
                                                                                              ]
                                                              );
        $text_password_body = html_entity_decode($text_password_body);
        $text_password_body .=  TemplateEmailAdmin::getTemplateEmailSignature();
        $text_password_body .=  TemplateEmailAdmin::getTemplateEmailTextFooter();

        $CLICSHOPPING_Mail->clicMail($QcheckCustomer->value('customers_firstname') . ' ' . $QcheckCustomer->value('customers_lastname'), $QcheckCustomer->value('customers_email_address'), $CLICSHOPPING_Customer->getDef('email_password_reminder_subject', ['store_name' => STORE_NAME]), nl2br(sprintf($text_password_body, $QcheckCustomer->value('customers_email_address'), $newpass )), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customer->getDef('text_new_password') . '&nbsp;' . ($QcheckCustomer->value('customers_firstname') . ' ' . $QcheckCustomer->value('customers_lastname')), 'success');
      }

      $CLICSHOPPING_Customer->redirect('Customers');
    }
  }