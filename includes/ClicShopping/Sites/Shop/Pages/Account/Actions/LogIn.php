<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Hash;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class LogIn extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
    $CLICSHOPPING_Template = Registry::get('Template');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Language = Registry::get('Language');

    $this->page->setFile('login.php');

// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
    if (Registry::get('Session')->hasStarted() === false) {
      if (!isset($_GET['cookie_test'])) {
        $all_get = CLICSHOPPING::getAllGET([
          'Account',
          'LogIn',
          'Process'
        ]);

        CLICSHOPPING::redirect(null, 'Account&LogIn&' . $all_get . (empty($all_get) ? '' : '&') . 'cookie_test=1');
      }

      CLICSHOPPING::redirect(null, 'Info&CookieUsage');
    }

    $CLICSHOPPING_Language->loadDefinitions('login');

    if (isset($_POST['formid']) && ($_POST['formid'] === $_SESSION['sessiontoken'])) {
      $error = false;

      $email_address = HTML::sanitize($_POST['email_address']);
      $password = HTML::sanitize($_POST['password']);

      if (CLICSHOPPING_TOTP_CATALOG == 'True') {
        $_SESSION['email_address'] = $email_address;
        $_SESSION['password'] = $password;

        CLICSHOPPING::redirect(null, 'Account&LogInAuth');
      }

// Check if email exists
      $array_sql = [
        'customers_id',
        'customers_password'];

      $Qcheck = $CLICSHOPPING_Db->get('customers', $array_sql, ['customers_email_address' => $email_address], null, 1);

// login content module must return $login_customer_id as an integer after successful customer authentication
      $_SESSION['login_customer_id'] = false;

      if ($Qcheck->fetch() === false) {
        $error = true;
      } else {
        if (!Hash::verify($password, $Qcheck->value('customers_password'))) {
          $error = true;
        } else {
          $_SESSION['login_customer_id'] = $Qcheck->valueInt('customers_id');
        }
      }

      if ($error === true) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_login_error'), 'error');
      }
    }

    $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('login');

    $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, 'Account&LogIn'));
  }
}