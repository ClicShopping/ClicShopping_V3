<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\Registry;

/**
 * Function select a zone
 *
 * @param string $zone_id text
 * @return string zone['zone_name'], the zone name of the country
 */
function clic_cfg_use_funtion_get_zone_name($zone_id)
{
  $Qzone = Registry::get('Db')->get('zones', 'zone_name', ['zone_id' => (int)$zone_id]);


  if ($Qzone->fetch() === false) {
    return $zone_id;
  } else {
    return $Qzone->value('zone_name');
  }
}