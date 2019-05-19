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

  use ClicShopping\OM\HTTP;

  class GetNews extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $result = [
        'rpcStatus' => -1
      ];

      $response = @json_decode(HTTP::getResponse([
//            'url' => ''
        /*
         * {"url":"http:\/\/altfarm.mediaplex.com\/ad\/ck\/3484-35557-8030-118","image":"https:\/\/ssl.clicshopping.org\/public\/sites\/Website\/images\/partners\/en_US\/paypal_banner.gif","title":"PayPal","status_update":"Accept most payment types including PayPal, anywhere you do business. More ways to get paid and more tools to help you sell more. Easy to set up, easy to use. Get started to free."}
         */
        'url' => ''
      ]), true);

      if (is_array($response) && isset($response['title'])) {
        $result = $response;

        $result['rpcStatus'] = 1;
      }

      echo json_encode($result);
    }
  }
