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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\Password;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Hash;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Mail = Registry::get('Mail');

      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $error = false;
        $password_current = HTML::sanitize($_POST['password_current']);
        $password_new = HTML::sanitize($_POST['password_new']);
        $password_confirmation = HTML::sanitize($_POST['password_confirmation']);

        if (strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_new_error', ['min_length' => ENTRY_PASSWORD_MIN_LENGTH]), 'error');

        } elseif ($password_new != $password_confirmation) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_new_error_not_matching'), 'error');
        }

        if ($error === false) {
          $QcheckCustomer = $CLICSHOPPING_Db->prepare('select  customers_firstname,
                                                               customers_lastname,
                                                               customers_emal_address,
                                                               customers_password
                                                       from :table_customers
                                                       where customers_id = :customers_id
                                                       and customer_guest_account = 0
                                                      ');
          $QcheckCustomer->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $QcheckCustomer->execute();

          if (Hash::verify($password_current, $QcheckCustomer->value('customers_password'))) {
            $CLICSHOPPING_Db->save('customers', ['customers_password' => Hash::encrypt($password_new)], ['customers_id' => (int)$CLICSHOPPING_Customer->getID()]);

            $Qupdate = $CLICSHOPPING_Db->prepare('update :table_customers_info
                                                  set customers_info_date_account_last_modified = now()
                                                  where customers_info_id = :customers_info_id
                                               ');
            $Qupdate->bindInt(':customers_info_id', $CLICSHOPPING_Customer->getID());
            $Qupdate->execute();

            $message = CLICSHOPPING::getDef('email_new_password', ['new_password' => $password_current, 'store_name' => STORE_NAME, 'store_owner_email_address' => STORE_OWNER_EMAIL_ADDRESS]);

            $email_password_reminder_body = $message . "\n";
            $email_password_reminder_body .= TemplateEmail::getTemplateEmailTextFooter() . "\n";
            $email_password_reminder_body .= TemplateEmail::getTemplateEmailSignature();

            $email_subject = CLICSHOPPING::getDef('email_password_subject', ['store_name' => STORE_NAME]);

            $CLICSHOPPING_Mail->clicMail($QcheckCustomer->value('customers_firstname') . ' ' . $QcheckCustomer->value('customers_lastname'), $QcheckCustomer->value('customers_email_address'), $email_subject, $email_password_reminder_body, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_password_updated'), 'success');

            $CLICSHOPPING_Hooks->call('Password', 'Process');

            CLICSHOPPING::redirect(null, 'Account&Password');
          } else {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_current_password_not_matching'), 'error');
          }
        }
      }
    }
  }