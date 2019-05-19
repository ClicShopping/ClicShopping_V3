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

  namespace ClicShopping\Apps\Payment\PayPal\Sites\ClicShoppingAdmin\Pages\Home\Actions\Start;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;

  class Retrieve extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_PayPal = Registry::get('PayPal');

      $params = [
        'merchant_id' => CLICSHOPPING_APP_PAYPAL_START_MERCHANT_ID,
        'secret' => CLICSHOPPING_APP_PAYPAL_START_SECRET
      ];

      $result = HTTP::getResponse([
//          'url' => '', // {"rpcStatus":-100}
//          'url' => '',
        'url' => '{"rpcStatus":-100}',
        'parameters' => $params
      ]);

      if (!empty($result)) {
        $result = json_decode($result, true);

        if (isset($result['rpcStatus']) && ($result['rpcStatus'] === 1) && isset($result['account_type']) && in_array($result['account_type'], ['live', 'sandbox']) && isset($result['account_id']) && isset($result['api_username']) && isset($result['api_password']) && isset($result['api_signature'])) {
          if ($result['account_type'] == 'live') {
            $param_prefix = 'CLICSHOPPING_APP_PAYPAL_LIVE_';
          } else {
            $param_prefix = 'CLICSHOPPING_APP_PAYPAL_SANDBOX_';
          }

          $CLICSHOPPING_PayPal->saveCfgParam($param_prefix . 'SELLER_EMAIL', str_replace('_api1.', '@', $result['api_username']));
          $CLICSHOPPING_PayPal->saveCfgParam($param_prefix . 'SELLER_EMAIL_PRIMARY', str_replace('_api1.', '@', $result['api_username']));
          $CLICSHOPPING_PayPal->saveCfgParam($param_prefix . 'MERCHANT_ID', $result['account_id']);
          $CLICSHOPPING_PayPal->saveCfgParam($param_prefix . 'API_USERNAME', $result['api_username']);
          $CLICSHOPPING_PayPal->saveCfgParam($param_prefix . 'API_PASSWORD', $result['api_password']);
          $CLICSHOPPING_PayPal->saveCfgParam($param_prefix . 'API_SIGNATURE', $result['api_signature']);

          $CLICSHOPPING_PayPal->deleteCfgParam('CLICSHOPPING_APP_PAYPAL_START_MERCHANT_ID');
          $CLICSHOPPING_PayPal->deleteCfgParam('CLICSHOPPING_APP_PAYPAL_START_SECRET');

          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_onboarding_success'), 'success', 'PayPal');
        } else {
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_onboarding_retrieve_error'), 'error', 'PayPal');
        }
      } else {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_onboarding_retrieve_connection_error'), 'error', 'PayPal');
      }

      $CLICSHOPPING_PayPal->redirect('Credentials');
    }
  }
