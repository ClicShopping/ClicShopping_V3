<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  /**
   * Function select a zone
   *
   * @param string text
   * @return string zone['zone_name'], the zone name of the country
   *
   * clic_cfg_get_zone_name
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