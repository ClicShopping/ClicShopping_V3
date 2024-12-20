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

/*
 * List all zones or specific zone by Country and Zones country
 *
 */
/**
 * @param $default
 * @param $key
 * @return string
 */

function clic_cfg_set_zones_pulldown_menu($default, $key = null)
{
  $CLICSHOPPING_Address = Registry::get('Address');

  $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

  $zones_array = [];

  foreach ($CLICSHOPPING_Address->getZones() as $zone) {
    $zones_array[] = ['id' => $zone['id'],
      'text' => $zone['name'],
      'group' => $zone['country_name']
    ];
  }

  return HTML::selectMenu($name, $zones_array, $default);
}
