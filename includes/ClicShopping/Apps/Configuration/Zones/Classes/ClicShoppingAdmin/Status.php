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

  namespace ClicShopping\Apps\Configuration\Zones\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;

  class Status
  {

    protected $zones_id;
    protected $status;

    /**
     * Status zones - Sets the status of a zone
     *
     * @param string products_id, status
     * @return string status on or off
     *
     */
    public static function getZonesStatus(int $zones_id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status === 1) {
        return $CLICSHOPPING_Db->save('zones', ['zone_status' => 1],
          ['zone_id' => (int)$zones_id]
        );

      } elseif ($status === 0) {
        return $CLICSHOPPING_Db->save('zones', ['zone_status' => 0],
          ['zone_id' => (int)$zones_id]
        );
      } else {
        return -1;
      }
    }
  }
