<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;

use ClicShopping\Apps\Tools\SecurityCheck\Classes\IpRestriction as Reject;
/**
 * This class implements the IpRestriction service for the ClicShoppingAdmin namespace.
 * It enforces IP-based restrictions on the admin area by checking and managing IP rules.
 */
class IpRestriction implements \ClicShopping\OM\ServiceInterface
{
  /**
   * @return bool
   */
  public static function start(): bool
  {
    $ip_restriction = Reject::checkAllIpAdminRestriction();
    Reject::saveIpRestriction();

    if ($ip_restriction === true) {
      CLICSHOPPING::redirect('../offline.php');
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

