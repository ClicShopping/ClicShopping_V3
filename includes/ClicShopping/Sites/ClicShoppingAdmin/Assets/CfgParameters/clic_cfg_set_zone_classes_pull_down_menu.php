<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

/**
 * @param $default
 * @param $key
 * @return string
 */
function clic_cfg_set_zone_classes_pull_down_menu($default, $key = null)
{
  $CLICSHOPPING_Db = Registry::get('Db');

  $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

  $zone_class_array = array(array('id' => 0,
    'text' => CLICSHOPPING::getDef('text_none')
  )
  );

  $Qzones = $CLICSHOPPING_Db->query('select geo_zone_id,
                                        geo_zone_name
                                 from :table_geo_zones
                                 order by geo_zone_name
                                ');
  $Qzones->execute();

  while ($Qzones->fetch()) {
    $zone_class_array[] = array('id' => $Qzones->valueInt('geo_zone_id'),
      'text' => $Qzones->value('geo_zone_name'));
  }

  return HTML::selectMenu($name, $zone_class_array, $default);
}


