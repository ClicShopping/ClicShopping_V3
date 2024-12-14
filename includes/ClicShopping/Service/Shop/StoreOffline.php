<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\Shop;

use ClicShopping\OM\CLICSHOPPING;
/**
 * Service class to handle the "Store Offline" mode in the application.
 * This service checks if the store is in offline mode and redirects users
 * who are not on the allowed IP address list to an offline page.
 */
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
