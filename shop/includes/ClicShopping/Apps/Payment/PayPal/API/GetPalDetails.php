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

  class GetPalDetails extends \ClicShopping\Apps\Payment\PayPal\APIAbstract
  {
    public function execute(array $extra_params = null)
    {
      $params = [
        'METHOD' => 'GetPalDetails',
        'USER' => $this->app->getCredentials('EC', 'username'),
        'PWD' => $this->app->getCredentials('EC', 'password'),
        'SIGNATURE' => $this->app->getCredentials('EC', 'signature')
      ];

      if (!empty($extra_params)) {
        $params = array_merge($params, $extra_params);
      }

      $response = $this->getResult($params);

      return [
        'res' => $response,
        'success' => isset($response['PAL']),
        'req' => $params
      ];
    }
  }
