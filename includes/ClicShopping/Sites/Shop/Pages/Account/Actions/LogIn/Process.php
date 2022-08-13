<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\LogIn;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\Sites\Shop;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
      if (Registry::get('Session')->hasStarted() === false) {
        if (!isset($_GET['cookie_test'])) {
          $all_get = CLICSHOPPING::getAllGET();

          CLICSHOPPING::redirect(null, 'Account&LogIn&' . $all_get . (empty($all_get) ? '' : '&') . 'cookie_test=1');
        }

        CLICSHOPPING::redirect(null, 'Info&Cookies');
      }

      if (isset($_SESSION['login_customer_id'])) {
        $login_customer_id = $_SESSION['login_customer_id'];
      } else {
        $login_customer_id = 0;
      }

      if (is_numeric($login_customer_id) && ($login_customer_id > 0)) {
        if ($login_customer_id > 0) {
          $CLICSHOPPING_Customer->setData($login_customer_id);
        }

        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_customers_info
                                              set customers_info_date_of_last_logon = now(),
                                                  customers_info_number_of_logons = customers_info_number_of_logons+1,
                                                  password_reset_key = null,
                                                  password_reset_date = null
                                              where customers_info_id = :customers_info_id
                                            ');
        $Qupdate->bindInt(':customers_info_id', $login_customer_id);
        $Qupdate->execute();

        $CLICSHOPPING_Hooks->call('Login', 'Process');
// restore cart contents
        $CLICSHOPPING_ShoppingCart->getRestoreContents();

        $CLICSHOPPING_NavigationHistory->removeCurrentPage();

        if ($CLICSHOPPING_NavigationHistory->hasSnapshot()) {
          $CLICSHOPPING_NavigationHistory->redirectToSnapshot();
        } else {
          CLICSHOPPING::redirect();
        }
      }
    }
  }