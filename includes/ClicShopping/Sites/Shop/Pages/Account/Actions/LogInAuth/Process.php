<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\LogInAuth;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Sites\Common\Topt;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop\TemplateEmail;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {
    private mixed $Customer;
    private mixed $Db;

    public function __construct() {
      $this->Db = Registry::get('Db');
      $this->Customer = Registry::get('Customer');
    }


    /**
     * @return void
     */
    public function sentEmail() :void
    {
      $CLICSHOPPING_Mail = Registry::get('Mail');

      if ($this->Customer->getCustomerIp() != HTTP::getIpAddress()) {
// Content email
        $template_email_signature = TemplateEmail::getTemplateEmailSignature();
        $template_email_footer = TemplateEmail::getTemplateEmailTextFooter();
        $email_subject = CLICSHOPPING::getDef('email_subject', ['store_name' => STORE_NAME]);
        $message = CLICSHOPPING::getDef('text_message', ['address_ip' => HTTP::getIpAddress(), 'provider' => HTTP::getProviderNameCustomer()]);
        $email_text = STORE_NAME . ',<br /><br />' . $message . '<br /><br />' . $template_email_signature . '<br /><br />' . $template_email_footer;

// Email send
        $message = $email_text;
        $message = str_replace('src="/', 'src="' . HTTP::typeUrlDomain() . '/', $message);
        $CLICSHOPPING_Mail->addHtmlCkeditor($message);

        $from = STORE_OWNER_EMAIL_ADDRESS;

        $CLICSHOPPING_Mail->send($this->Customer->getEmailAddress(), $this->Customer->getName(), null, $from, $email_subject);
      }
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $CLICSHOPPING_Hooks->call('LogInAuth', 'postProcess');

      // redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
      if (Registry::get('Session')->hasStarted() === false) {
        if (!isset($_GET['cookie_test'])) {
          $all_get = CLICSHOPPING::getAllGET();

          CLICSHOPPING::redirect(null, 'Account&LogInAuth&' . $all_get . (empty($all_get) ? '' : '&') . 'cookie_test=1');
        }

        CLICSHOPPING::redirect(null, 'Info&Cookies');
      }

      $error = true;

// Check the topt
      if (isset($_POST['tfa_code'])) {
        $tfaCode = HTML::sanitize($_POST['tfa_code']);

        if (empty($tfaCode)){
          CLICSHOPPING::redirect(null, 'Account&LogInAuth');
        } else {
          if (!empty(Topt::checkToptloginCustomer($_SESSION['email_address']))) {
            if (Topt::getVerifyAuth($_SESSION['tfa_secret'], $tfaCode) === true) {
              $sql_data_array = ['client_computer_ip' => HTTP::getIpAddress()];

              $this->Db->save('customers', $sql_data_array, ['customers_id' => (int)$_SESSION['customer_id']]);
              $error = false;
            } else {
              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_code_auth_invalid'), 'error');
              $error = true;
              unset($_SESSION['user_secret']);
            }
          } else {
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_code_auth_invalid'), 'error');
            $error = true;

            unset($_SESSION['user_secret']);
          }
        }
      } else {
        CLICSHOPPING::redirect(null, 'Account&LogInAuth');
      }

// activate the login session or not
      if (isset($_SESSION['email_address']) && isset($_SESSION['password']) && $error === false) {
// Check if email exists
        $array_sql = ['customers_id'];

        $Qcheck = $this->Db->get('customers',  $array_sql,  ['customers_email_address' => $_SESSION['email_address']], null, 1);

        if ($Qcheck->fetch()) {
          $_SESSION['login_customer_id'] = $Qcheck->valueInt('customers_id');
        }
      } else {
        CLICSHOPPING::redirect(null, 'Account&LogInAuth');
      }

      if (isset($_SESSION['login_customer_id'])) {
        $login_customer_id = $_SESSION['login_customer_id'];
      } else {
        $login_customer_id = 0;
      }

      if (is_numeric($login_customer_id) && ($login_customer_id > 0)) {
        if ($login_customer_id > 0) {
          $this->Customer->setData($login_customer_id);
          unset($_SESSION['customer_id']);
          unset($_SESSION['password']);
          unset($_SESSION['email_address']);
          unset($_SESSION['tfa_secret']);
          unset($_SESSION['user_secret']);
        }

        $Qupdate = $this->Db->prepare('update :table_customers_info
                                          set customers_info_date_of_last_logon = now(),
                                              customers_info_number_of_logons = customers_info_number_of_logons+1,
                                              password_reset_key = null,
                                              password_reset_date = null
                                          where customers_info_id = :customers_info_id
                                        ');
        $Qupdate->bindInt(':customers_info_id', $login_customer_id);
        $Qupdate->execute();

        $this->sentEmail();
      } else {
        $this->sentEmail();
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }

// restore cart contents
      $CLICSHOPPING_ShoppingCart->getRestoreContents();

      $CLICSHOPPING_NavigationHistory->removeCurrentPage();

      $CLICSHOPPING_Hooks->call('LogInAuth', 'Process');

      if ($CLICSHOPPING_NavigationHistory->hasSnapshot()) {
        $CLICSHOPPING_NavigationHistory->redirectToSnapshot();
      } else {
        CLICSHOPPING::redirect();
      }
    }
  }