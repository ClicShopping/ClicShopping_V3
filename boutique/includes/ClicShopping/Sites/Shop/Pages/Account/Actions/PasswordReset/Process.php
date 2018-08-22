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

  use ClicShopping\Sites\Shop\Pages\Account\Classes\PasswordReset as Reset;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      global $email_address;

      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $Qc = Reset::getPasswordResetCheckEmailAddress($email_address);

      if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {

        $password_new = HTML::sanitize($_POST['password']);
        $password_confirmation = HTML::sanitize($_POST['confirmation']);

        if (strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_new_error', ['min_length' => ENTRY_PASSWORD_MIN_LENGTH]), 'error', 'password_reset');

        } elseif ($password_new != $password_confirmation) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_new_error_not_matching'), 'error', 'password_reset');
        }

        if ($error === false) {

          $CLICSHOPPING_Db->save('customers', ['customers_password' => Hash::encrypt($password_new)],
                                       ['customers_id' => (int)$Qc->valueInt('customers_id')]
                          );


          $sql_array = ['customers_info_date_account_last_modified' => 'now()',
                         'password_reset_key' => 'null',
                         'password_reset_date' => 'null'
                       ];

          $CLICSHOPPING_Db->save('customers_info', $sql_array, ['customers_info_id' => (int)$Qc->valueInt('customers_id')]);

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_password_reset'), 'error', 'password_reset');

          $CLICSHOPPING_Mail->clicMail($Qc->value('customers_firstname') . ' ' . $Qc->value('customers_lastname'), $email_address, CLICSHOPPING::getDef('text_email_subject', ['store_name' => STORE_NAME]), CLICSHOPPING::getDef('text_email_body', ['store_name' => STORE_NAME]), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);

          $CLICSHOPPING_Hooks->call('PasswordReset', 'Process');

          CLICSHOPPING::redirect('index.php', 'Account&LogIn');
        }
      }
    }
  }