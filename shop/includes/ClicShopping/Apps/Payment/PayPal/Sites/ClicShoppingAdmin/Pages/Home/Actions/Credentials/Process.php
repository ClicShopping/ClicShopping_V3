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

  namespace ClicShopping\Apps\Payment\PayPal\Sites\ClicShoppingAdmin\Pages\Home\Actions\Credentials;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_PayPal = Registry::get('PayPal');

      $current_module = $this->page->data['current_module'];

      $data = [];

      if ($current_module == 'PP') {
        $data = [
          'CLICSHOPPING_APP_PAYPAL_LIVE_SELLER_EMAIL' => isset($_POST['live_email']) ? HTML::sanitize($_POST['live_email']) : '',
          'CLICSHOPPING_APP_PAYPAL_LIVE_SELLER_EMAIL_PRIMARY' => isset($_POST['live_email_primary']) ? HTML::sanitize($_POST['live_email_primary']) : '',
          'CLICSHOPPING_APP_PAYPAL_LIVE_MERCHANT_ID' => isset($_POST['live_merchant_id']) ? HTML::sanitize($_POST['live_merchant_id']) : '',
          'CLICSHOPPING_APP_PAYPAL_LIVE_API_USERNAME' => isset($_POST['live_username']) ? HTML::sanitize($_POST['live_username']) : '',
          'CLICSHOPPING_APP_PAYPAL_LIVE_API_PASSWORD' => isset($_POST['live_password']) ? HTML::sanitize($_POST['live_password']) : '',
          'CLICSHOPPING_APP_PAYPAL_LIVE_API_SIGNATURE' => isset($_POST['live_signature']) ? HTML::sanitize($_POST['live_signature']) : '',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_SELLER_EMAIL' => isset($_POST['sandbox_email']) ? HTML::sanitize($_POST['sandbox_email']) : '',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_SELLER_EMAIL_PRIMARY' => isset($_POST['sandbox_email_primary']) ? HTML::sanitize($_POST['sandbox_email_primary']) : '',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_MERCHANT_ID' => isset($_POST['sandbox_merchant_id']) ? HTML::sanitize($_POST['sandbox_merchant_id']) : '',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_API_USERNAME' => isset($_POST['sandbox_username']) ? HTML::sanitize($_POST['sandbox_username']) : '',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_API_PASSWORD' => isset($_POST['sandbox_password']) ? HTML::sanitize($_POST['sandbox_password']) : '',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_API_SIGNATURE' => isset($_POST['sandbox_signature']) ? HTML::sanitize($_POST['sandbox_signature']) : ''
        ];
      } elseif ($current_module == 'PF') {
        $data = [
          'CLICSHOPPING_APP_PAYPAL_PF_LIVE_PARTNER' => isset($_POST['live_partner']) ? HTML::sanitize($_POST['live_partner']) : '',
          'CLICSHOPPING_APP_PAYPAL_PF_LIVE_VENDOR' => isset($_POST['live_vendor']) ? HTML::sanitize($_POST['live_vendor']) : '',
          'CLICSHOPPING_APP_PAYPAL_PF_LIVE_USER' => isset($_POST['live_user']) ? HTML::sanitize($_POST['live_user']) : '',
          'CLICSHOPPING_APP_PAYPAL_PF_LIVE_PASSWORD' => isset($_POST['live_password']) ? HTML::sanitize($_POST['live_password']) : '',
          'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_PARTNER' => isset($_POST['sandbox_partner']) ? HTML::sanitize($_POST['sandbox_partner']) : '',
          'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_VENDOR' => isset($_POST['sandbox_vendor']) ? HTML::sanitize($_POST['sandbox_vendor']) : '',
          'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_USER' => isset($_POST['sandbox_user']) ? HTML::sanitize($_POST['sandbox_user']) : '',
          'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_PASSWORD' => isset($_POST['sandbox_password']) ? HTML::sanitize($_POST['sandbox_password']) : ''
        ];
      }

      foreach ($data as $key => $value) {
        $CLICSHOPPING_PayPal->saveCfgParam($key, $value);
      }

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_credentials_saved_success'), 'success', 'PayPal');

      $CLICSHOPPING_PayPal->redirect('Credentials&module=' . $current_module);
    }
  }
