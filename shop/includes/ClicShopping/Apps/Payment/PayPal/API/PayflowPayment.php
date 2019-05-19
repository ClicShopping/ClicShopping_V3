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

  namespace ClicShopping\Apps\Payment\PayPal\API;

  use ClicShopping\OM\HTTP;

  class PayflowPayment extends \ClicShopping\Apps\Payment\PayPal\APIAbstract
  {
    protected $type = 'payflow';

    public function execute(array $extra_params = null)
    {
      $params = [
        'USER' => $this->app->hasCredentials('DP', 'payflow_user') ? $this->app->getCredentials('DP', 'payflow_user') : $this->app->getCredentials('DP', 'payflow_vendor'),
        'VENDOR' => $this->app->getCredentials('DP', 'payflow_vendor'),
        'PARTNER' => $this->app->getCredentials('DP', 'payflow_partner'),
        'PWD' => $this->app->getCredentials('DP', 'payflow_password'),
        'TENDER' => 'C',
        'TRXTYPE' => (CLICSHOPPING_APP_PAYPAL_DP_TRANSACTION_METHOD == '1') ? 'S' : 'A',
        'CUSTIP' => HTTP::getIpAddress(),
        'BUTTONSOURCE' => $this->app->getIdentifier()
      ];

      if (!empty($extra_params)) {
        $params = array_merge($params, $extra_params);
      }

      $headers = [];

      if (isset($params['_headers'])) {
        $headers = $params['_headers'];

        unset($params['_headers']);
      }

      $response = $this->getResult($params, $headers);

      return [
        'res' => $response,
        'success' => ($response['RESULT'] == '0'),
        'req' => $params
      ];
    }
  }
