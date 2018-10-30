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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\PasswordReset;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Hash;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop\TemplateEmail;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $error = false;
        $email_address = HTML::sanitize($_GET['account']);
        $key = HTML::sanitize($_GET['key']);

        $Qcheck = $CLICSHOPPING_Db->prepare('select c.customers_id,
                                                    c.customers_email_address,
                                                    ci.password_reset_key,
                                                    ci.password_reset_date
                                             from :table_customers c,
                                                  :table_customers_info ci
                                             where c.customers_email_address = :customers_email_address
                                             and c.customers_id = ci.customers_info_id
                                             and c.customer_guest_account = 0
                                             limit 1
                                           ');

        $Qcheck->bindValue(':customers_email_address', $email_address);
        $Qcheck->execute();

        $password_new = HTML::sanitize($_POST['password']);
        $password_confirmation = HTML::sanitize($_POST['confirmation']);

        if ((strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) && !isset($key)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_new_error', ['min_length' => ENTRY_PASSWORD_MIN_LENGTH]), 'error', 'password_reset');

        } elseif (($password_new != $password_confirmation) && !isset($key)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_new_error_not_matching'), 'error', 'password_reset');
        }

        if ($error === false) {

          $CLICSHOPPING_Db->save('customers', ['customers_password' => Hash::encrypt($password_new)],
                                              ['customers_id' => (int)$Qcheck->valueInt('customers_id')]
                                );


          $sql_array = ['customers_info_date_account_last_modified' => 'now()',
                         'password_reset_key' => 'null',
                         'password_reset_date' => 'null'
                       ];

          $CLICSHOPPING_Db->save('customers_info', $sql_array, ['customers_info_id' => (int)$Qcheck->valueInt('customers_id')]);

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_password_reset'), 'success', 'password_reset');

          $email_text_body = ' <br />' . TemplateEmail::getTemplateEmailTextFooter();
          $email_text_body .= ' <br />' . TemplateEmail::getTemplateEmailSignature();

          $CLICSHOPPING_Mail->clicMail($Qcheck->value('customers_firstname') . ' ' . $Qcheck->value('customers_lastname'), $email_address, CLICSHOPPING::getDef('text_email_subject', ['store_name' => STORE_NAME]), CLICSHOPPING::getDef('text_email_body', ['store_name' => STORE_NAME]) . $email_text_body, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);

          $CLICSHOPPING_Hooks->call('PasswordReset', 'Process');

          CLICSHOPPING::redirect('index.php', 'Account&LogIn');
        }
      }
    }
  }