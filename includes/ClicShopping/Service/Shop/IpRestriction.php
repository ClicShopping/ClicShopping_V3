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

class IpRestriction implements \ClicShopping\OM\ServiceInterface
{
  /**
   * @return bool
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
   * @return bool
   */
  public static function stop(): bool
  {
    return true;
  }
}

