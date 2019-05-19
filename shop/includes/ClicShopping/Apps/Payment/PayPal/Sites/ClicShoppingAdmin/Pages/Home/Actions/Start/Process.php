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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_PayPal = Registry::get('PayPal');

      if (isset($_GET['type']) && in_array($_GET['type'], [
          'live',
          'sandbox'
        ])) {
        $params = [
          'return_url' => $CLICSHOPPING_PayPal->link('Start&Retrieve'),
          'type' => $_GET['type'],
          'site_url' => CLICSHOPPING::link('Shop/index.php', null, false),
          'site_currency' => DEFAULT_CURRENCY
        ];

        if (!empty(STORE_OWNER_EMAIL_ADDRESS) && (filter_var(STORE_OWNER_EMAIL_ADDRESS, FILTER_VALIDATE_EMAIL) !== false)) {
          $params['email'] = STORE_OWNER_EMAIL_ADDRESS;
        }

        if (!empty(STORE_OWNER)) {
          $name_array = explode(' ', STORE_OWNER, 2);

          $params['firstname'] = $name_array[0];
          $params['surname'] = isset($name_array[1]) ? $name_array[1] : '';
        }

        if (!empty(STORE_NAME)) {
          $params['site_name'] = STORE_NAME;
        }

        $result = HTTP::getResponse([
//              'url' => '', // {"rpcStatus":-100}
          'url' => '',
          'parameters' => $params
        ]);

        $result = json_decode($result, true);

        if (!empty($result) && is_array($result) && isset($result['rpcStatus'])) {
          if (($result['rpcStatus'] === 1) && isset($result['merchant_id']) && (preg_match('/^[A-Za-z0-9]{32}$/', $result['merchant_id']) === 1) && isset($result['redirect_url']) && isset($result['secret'])) {
            $CLICSHOPPING_PayPal->saveCfgParam('CLICSHOPPING_APP_PAYPAL_START_MERCHANT_ID', $result['merchant_id']);
            $CLICSHOPPING_PayPal->saveCfgParam('CLICSHOPPING_APP_PAYPAL_START_SECRET', $result['secret']);

            HTTP::redirect($result['redirect_url']);
          } elseif ($result['rpcStatus'] === -110) {
            $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_onboarding_currently_unavailable_error'), 'error', 'PayPal');
          } else {
            $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_onboarding_initialization_error'), 'error', 'PayPal');
          }
        } else {
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_onboarding_connection_error'), 'error', 'PayPal');
        }
      } else {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PayPal->getDef('alert_onboarding_account_type_error'), 'error', 'PayPal');
      }

      $CLICSHOPPING_PayPal->redirect('Credentials');
    }
  }
