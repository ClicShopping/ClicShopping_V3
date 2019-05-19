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

  namespace ClicShopping\Apps\Payment\PayPal\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Credentials extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_PayPal = Registry::get('PayPal');

      $this->page->setFile('credentials.php');
      $this->page->data['action'] = 'Credentials';

      $CLICSHOPPING_PayPal->loadDefinitions('ClicShoppingAdmin/credentials');

      $modules = [
        'PP',
        'PF'
      ];

      $this->page->data['current_module'] = (isset($_GET['module']) && in_array($_GET['module'], $modules) ? $_GET['module'] : $modules[0]);

      $data = [
        'CLICSHOPPING_APP_PAYPAL_LIVE_SELLER_EMAIL',
        'CLICSHOPPING_APP_PAYPAL_LIVE_SELLER_EMAIL_PRIMARY',
        'CLICSHOPPING_APP_PAYPAL_LIVE_API_USERNAME',
        'CLICSHOPPING_APP_PAYPAL_LIVE_API_PASSWORD',
        'CLICSHOPPING_APP_PAYPAL_LIVE_API_SIGNATURE',
        'CLICSHOPPING_APP_PAYPAL_LIVE_MERCHANT_ID',
        'CLICSHOPPING_APP_PAYPAL_SANDBOX_SELLER_EMAIL',
        'CLICSHOPPING_APP_PAYPAL_SANDBOX_SELLER_EMAIL_PRIMARY',
        'CLICSHOPPING_APP_PAYPAL_SANDBOX_API_USERNAME',
        'CLICSHOPPING_APP_PAYPAL_SANDBOX_API_PASSWORD',
        'CLICSHOPPING_APP_PAYPAL_SANDBOX_API_SIGNATURE',
        'CLICSHOPPING_APP_PAYPAL_SANDBOX_MERCHANT_ID',
        'CLICSHOPPING_APP_PAYPAL_PF_LIVE_PARTNER',
        'CLICSHOPPING_APP_PAYPAL_PF_LIVE_VENDOR',
        'CLICSHOPPING_APP_PAYPAL_PF_LIVE_USER',
        'CLICSHOPPING_APP_PAYPAL_PF_LIVE_PASSWORD',
        'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_PARTNER',
        'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_VENDOR',
        'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_USER',
        'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_PASSWORD'
      ];

      foreach ($data as $key) {
        if (!defined($key)) {
          $CLICSHOPPING_PayPal->saveCfgParam($key, '');
        }
      }
    }
  }
