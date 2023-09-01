<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\BannerManager\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{
  /**
   * Status modification of banners - Sets the status of a banner
   * @param int $banners_id
   * @param int $status
   * @return int
   */
  public static function setBannerStatus(int $banners_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('banners', [
        'status' => 1,
        'expires_impressions' => NULL,
        'expires_date' => NULL,
        'date_status_change' => NULL
      ],
        ['banners_id' => (int)$banners_id]
      );

    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('banners', [
        'status' => 0,
        'date_status_change' => 'now()'
      ],
        ['banners_id' => (int)$banners_id]
      );

    } else {
      return -1;
    }
  }
}