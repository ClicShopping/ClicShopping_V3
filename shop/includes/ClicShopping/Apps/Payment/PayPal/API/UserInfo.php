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

  class UserInfo extends \ClicShopping\Apps\Payment\PayPal\APIAbstract
  {
    protected $type = 'login';

    public function execute(array $params = null)
    {
      $this->url = 'https://api.' . ($this->server != 'live' ? 'sandbox.' : '') . 'paypal.com/v1/identity/openidconnect/userinfo/?schema=openid&access_token=' . $params['access_token'];

      $response = $this->getResult($params);

      return [
        'res' => $response,
        'success' => (is_array($response) && !isset($response['error'])),
        'req' => $params
      ];
    }
  }
