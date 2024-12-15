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

use ClicShopping\Apps\Tools\SecurityCheck\Classes\IpRestriction as Reject;
/**
 * This class provides the service for handling IP restrictions in the shop.
 * It implements the ServiceInterface and ensures that unauthorized IPs are redirected to an offline page.
 */
class IpRestriction implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Starts the process of checking IP restrictions and handling the redirection if necessary.
   *
   * This method performs a check for all shop-specific IP restrictions, saves the current restriction state,
   * and redirects to an offline page if the IP restriction condition is met.
   *
   * @return bool Returns true after completing the process.
   */
  public static function start(): bool
  {
    $ip_restriction = Reject::checkAllIpShopRestriction();
    Reject::saveIpRestriction();

    if ($ip_restriction === true) {
      CLICSHOPPING::redirect('offline.php');
    }

    return true;
  }

  /**
   * Stops the current process or action.
   *
   * @return bool Returns true indicating the process was successfully stopped.
   */
  public static function stop(): bool
  {
    return true;
  }
}

