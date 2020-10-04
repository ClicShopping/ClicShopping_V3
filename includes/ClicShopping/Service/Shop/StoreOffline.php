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

  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class StoreOffline implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
      if (STORE_OFFLINE == 'true') {

        $allowed_ip = false;
        $ips = explode(',', STORE_OFFLINE_ALLOW);

        foreach ($ips as $ip) {
          if (trim($ip) === $_SERVER['REMOTE_ADDR']) {
            $allowed_ip = true;
            break;
          }
        }

        if ($allowed_ip === false) {
          CLICSHOPPING::redirect('offline.php');
        }
      }

      return true;
    }

    public static function stop(): bool
    {
      return true;
    }
  }
