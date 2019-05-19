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

  namespace ClicShopping\Apps\Payment\PayPal\Sites\ClicShoppingAdmin\Pages\Home\Actions\RPC;

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class GetBalance extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_PayPal = Registry::get('PayPal');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      $result = [
        'rpcStatus' => -1
      ];

      if (isset($_GET['type']) && in_array($_GET['type'], [
          'live',
          'sandbox'
        ])) {
        $PayPalCache = new Cache('app_paypal-balance');

        if (!isset($_GET['force']) && $PayPalCache->exists(15)) {
          $response = $PayPalCache->get();
        } else {
          $response = $CLICSHOPPING_PayPal->getApiResult('APP', 'GetBalance', null, $_GET['type']);

          if (is_array($response) && isset($response['ACK']) && ($response['ACK'] == 'Success')) {
            $PayPalCache->save($response);
          }
        }

        if (is_array($response) && isset($response['ACK']) && ($response['ACK'] == 'Success')) {
          $result['rpcStatus'] = 1;

          $counter = 0;

          while (true) {
            if (isset($response['L_AMT' . $counter]) && isset($response['L_CURRENCYCODE' . $counter])) {
              $balance = $response['L_AMT' . $counter];

              if (isset($CLICSHOPPING_Currencies->currencies[$response['L_CURRENCYCODE' . $counter]])) {
                $balance = $CLICSHOPPING_Currencies->format($balance, false, $response['L_CURRENCYCODE' . $counter]);
              }

              $result['balance'][$response['L_CURRENCYCODE' . $counter]] = $balance;

              $counter++;
            } else {
              break;
            }
          }
        }
      }

      echo json_encode($result);
    }
  }
