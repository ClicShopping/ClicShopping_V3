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

  class BMCreateButton extends \ClicShopping\Apps\Payment\PayPal\APIAbstract
  {
    public function execute(array $extra_params = null)
    {
      $params = [
        'USER' => $this->app->getCredentials('HS', 'username'),
        'PWD' => $this->app->getCredentials('HS', 'password'),
        'SIGNATURE' => $this->app->getCredentials('HS', 'signature'),
        'METHOD' => 'BMCreateButton',
        'BUTTONCODE' => 'TOKEN',
        'BUTTONTYPE' => 'PAYMENT'
      ];

      $l_params = [
        'business' => $this->app->getCredentials('HS', 'email'),
        'bn' => $this->app->getIdentifier()
      ];

      if (!empty($extra_params)) {
        $l_params = array_merge($l_params, $extra_params);
      }

      $counter = 0;

      foreach ($l_params as $key => $value) {
        $params['L_BUTTONVAR' . $counter] = $key . '=' . $value;

        $counter++;
      }

      $response = $this->getResult($params);

      return [
        'res' => $response,
        'success' => in_array($response['ACK'], ['Success', 'SuccessWithWarning']),
        'req' => $params
      ];
    }
  }
