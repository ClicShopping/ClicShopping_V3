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

  class GrantToken extends \ClicShopping\Apps\Payment\PayPal\APIAbstract
  {
    protected $type = 'login';

    public function execute(array $extra_params = null)
    {
      $params = [
        'client_id' => (CLICSHOPPING_APP_PAYPAL_LOGIN_STATUS == '1') ? CLICSHOPPING_APP_PAYPAL_LOGIN_LIVE_CLIENT_ID : CLICSHOPPING_APP_PAYPAL_LOGIN_SANDBOX_CLIENT_ID,
        'client_secret' => (CLICSHOPPING_APP_PAYPAL_LOGIN_STATUS == '1') ? CLICSHOPPING_APP_PAYPAL_LOGIN_LIVE_SECRET : CLICSHOPPING_APP_PAYPAL_LOGIN_SANDBOX_SECRET,
        'grant_type' => 'authorization_code'
      ];

      if (!empty($extra_params)) {
        $params = array_merge($params, $extra_params);
      }

      $response = $this->getResult($params);

      return [
        'res' => $response,
        'success' => (is_array($response) && !isset($response['error'])),
        'req' => $params
      ];
    }
  }
