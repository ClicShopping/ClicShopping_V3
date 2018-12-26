<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

/*
 * List all zones or specific zone by Country and Zones country
 *
 */

// clic_cfg_pull_down_zone_classes
  function clic_cfg_set_zones_pulldown_menu($default, $key = null) {
    $CLICSHOPPING_Address = Registry::get('Address');

    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zones_array = [];

    foreach ($CLICSHOPPING_Address->getZones() as $zone ) {
      $zones_array[] = ['id' => $zone['id'],
                        'text' => $zone['name'],
                        'group' => $zone['country_name']
                       ];
    }

    return HTML::selectMenu($name, $zones_array, $default);
  }
