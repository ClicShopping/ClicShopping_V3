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

  class GetExpressCheckoutDetails extends \ClicShopping\Apps\Payment\PayPal\APIAbstract
  {
    public function execute(array $extra_params = null)
    {
      $params = [
        'METHOD' => 'GetExpressCheckoutDetails'
      ];

      if ($this->app->hasCredentials('EC')) {
        $params['USER'] = $this->app->getCredentials('EC', 'username');
        $params['PWD'] = $this->app->getCredentials('EC', 'password');
        $params['SIGNATURE'] = $this->app->getCredentials('EC', 'signature');
      } else {
        $params['SUBJECT'] = $this->app->getCredentials('EC', 'email');
      }

      if (!empty($extra_params)) {
        $params = array_merge($params, $extra_params);
      }

      $response = $this->getResult($params);

      return [
        'res' => $response,
        'success' => in_array($response['ACK'], ['Success', 'SuccessWithWarning']),
        'req' => $params
      ];
    }
  }
