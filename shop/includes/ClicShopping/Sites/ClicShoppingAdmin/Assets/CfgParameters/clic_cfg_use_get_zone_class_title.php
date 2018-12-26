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
  use ClicShopping\OM\CLICSHOPPING;

  function clic_cfg_use_get_zone_class_title($id) {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ( $id == 0 ) {
      return CLICSHOPPING::getDef('text_none');
    } else {
      $Qclass = $CLICSHOPPING_Db->prepare('select geo_zone_name
                                     from :table_geo_zones
                                     where geo_zone_id = :geo_zone_id
                                     ');
      $Qclass->bindInt(':geo_zone_id', $id);
      $Qclass->execute();

      return $Qclass->value('geo_zone_name');
    }
  }
