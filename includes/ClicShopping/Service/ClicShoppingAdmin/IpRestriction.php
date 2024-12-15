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
   * Initiates the process of checking and handling IP restrictions for admin access.
   *
   * @return bool Returns true after processing the IP restriction checks and redirection if necessary.
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
   * Stops an operation or process and ensures a successful termination.
   *
   * @return bool Returns true to indicate the stop operation was successful.
   */
  public static function stop(): bool
  {
    return true;
  }
}

