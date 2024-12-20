<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Zones\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{
  /**
   * Updates the status of a specific zone in the database based on the provided parameters.
   *
   * @param int $zones_id The unique identifier of the zone to be updated.
   * @param int $status The new status to set for the zone; expected values are 1 (active) or 0 (inactive).
   * @return mixed Returns the result of the database operation if the status is valid; returns -1 otherwise.
   */
  public static function getZonesStatus(int $zones_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('zones', ['zone_status' => 1], ['zone_id' => (int)$zones_id]
      );

    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('zones', ['zone_status' => 0], ['zone_id' => (int)$zones_id]);
    } else {
      return -1;
    }
  }
}
