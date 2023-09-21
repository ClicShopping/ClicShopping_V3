<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Hash;
use ClicShopping\OM\Registry;
use ClicShopping\Sites\Common\Topt;

class LogInAuth extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
    $CLICSHOPPING_Template = Registry::get('Template');
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    $this->page->setFile('LogInAuth.php');

    if (CLICSHOPPING_TOTP_CATALOG == 'False') {
      CLICSHOPPING::redirect('Account&LogIn');
    }

    if (!isset($_SESSION['email_address']) || !isset($_SESSION['password'])) {
      unset($_SESSION['email_address']);
      unset($_SESSION['password']);
      CLICSHOPPING::redirect('Account&LogIn');
    } else {
      $email_address = $_SESSION['email_address'];
      $password = $_SESSION['password'];
    }

    // redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
    if (Registry::get('Session')->hasStarted() === false) {
      if (!isset($_GET['cookie_test'])) {
        $all_get = CLICSHOPPING::getAllGET([
          'Account',
          'LogInAuth',
          'Process'
        ]);

        CLICSHOPPING::redirect(null, 'Account&LogInAuth&' . $all_get . (empty($all_get) ? '' : '&') . 'cookie_test=1');
      }

      CLICSHOPPING::redirect(null, 'Info&CookieUsage');
    }

// Check if email exists
    $array_sql = [
      'customers_id',
      'customers_password',
      'double_authentification_secret'
    ];

    $Qcheck = $CLICSHOPPING_Db->get('customers', $array_sql, ['customers_email_address' => $email_address], null, 1);

// login content module must return $login_customer_id as an integer after successful customer authentication
    $_SESSION['login_customer_id'] = false;
    $error = false;

    if ($Qcheck->fetch() === false) {
      $error = true;
    } else {
      if (!Hash::verify($password, $Qcheck->value('customers_password'))) {
        $error = true;
      } else {
        $_SESSION['customer_id'] = $Qcheck->valueInt('customers_id');
        $error = false;
      }
    }

    if ($error === true && $_SESSION['login_customer_id'] === false) {
      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_login_error'), 'error');

      CLICSHOPPING::redirect(null, 'Account&LogIn');
    }

    $Qcheck = $CLICSHOPPING_Db->get('customers', 'double_authentification_secret', ['customers_email_address' => $email_address]);

    if (empty(Topt::checkToptloginCustomer($email_address))) {
      $_SESSION['tfa_secret'] = Topt::getTfaSecret();

      $update_array = ['double_authentification_secret' => $_SESSION['tfa_secret']];

      $CLICSHOPPING_Db->save('customers', $update_array, ['customers_email_address' => $email_address]);
    } else if (empty($_SESSION['tfa_secret'])) {
      $_SESSION['tfa_secret'] = $Qcheck->value('double_authentification_secret');
    }

// templates
    $this->page->setFile('login_auth.php');
//Content
    $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('login_auth');
//language
    $CLICSHOPPING_Language->loadDefinitions('login_auth');

    $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, 'Account&LogInAuth'));
  }
}