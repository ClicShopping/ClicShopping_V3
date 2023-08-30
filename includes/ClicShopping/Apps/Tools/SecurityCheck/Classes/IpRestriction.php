<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecurityCheck\Classes;

use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;

class IpRestriction
{
  /**
   * @return string|null
   */
  public static function getRemoteAddress(): ?string
  {
    $address_server = HTTP::getIpAddress();

    return $address_server;
  }

  /**
   * @return bool
   */
  public static function checkAllIpShopRestriction(): bool
  {
    $ClISHOPPING_db = Registry::get('Db');

    $allowed_ip = false;

    $Qrestriction = $ClISHOPPING_db->prepare('select distinct ip_restriction
                                                from :table_ip_restriction
                                                where ip_status_shop = 1
                                               ');
    $Qrestriction->execute();

    $restriction = $Qrestriction->fetchAll();

    foreach ($restriction as $value) {
      if (trim($value['0']) === static::getRemoteAddress()) {
        $allowed_ip = true;
        break;
      }
    }

    return $allowed_ip;
  }

  /**
   * Save ip
   */
  public static function saveIpRestriction(): void
  {
    $ClISHOPPING_db = Registry::get('Db');

    if (static::checkAllIpShopRestriction() === true || static::checkAllIpAdminRestriction() === true) {
      $ClISHOPPING_db->save('ip_restriction_stats', ['ip_remote' => static::getRemoteAddress()]);
    }
  }

  /**
   * @return bool
   */
  public static function checkAllIpAdminRestriction(): bool
  {
    $ClISHOPPING_db = Registry::get('Db');

    $allowed_ip = false;

    $Qrestriction = $ClISHOPPING_db->prepare('select distinct ip_restriction
                                                from :table_ip_restriction
                                                where ip_status_admin = 1
                                               ');
    $Qrestriction->execute();

    $restriction = $Qrestriction->fetchAll();

    foreach ($restriction as $value) {
      if (trim($value['0']) === static::getRemoteAddress()) {
        $allowed_ip = true;
        break;
      }
    }

    return $allowed_ip;
  }

  /**
   * Ip restrcition Shop Status
   * @param int $id
   * @param int $status
   * @return int
   */
  public static function getIpRestrictionShopStatus(int $id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('ip_restriction', ['ip_status_shop' => 1], ['id' => (int)$id]);
    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('ip_restriction', ['ip_status_shop' => 0], ['id' => (int)$id]);
    } else {
      return -1;
    }
  }

  /**
   * Ip restrcition Admin Status
   * @param int $id
   * @param int $status
   * @return
   */
  public static function getIpRestrictionAdminStatus(int $id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('ip_restriction', ['ip_status_admin' => 1], ['id' => (int)$id]);
    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('ip_restriction', ['ip_status_admin' => 0], ['id' => (int)$id]);
    } else {
      return -1;
    }
  }
}