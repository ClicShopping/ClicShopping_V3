<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

/**
 * @param $zone_id
 * @return string
 */
function clic_cfg_set_zones_list_pull_down_menu($zone_id)
{
  $CLICSHOPPING_Address = Registry::get('Address');

  return HTML::selectMenu('configuration_value', $CLICSHOPPING_Address->getCountryZones(STORE_COUNTRY), $zone_id);
}