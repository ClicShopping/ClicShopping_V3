<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Currency\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{

  protected $countries_id;
  protected $status;

  /**
   * @param $currencies_id
   * @param $status
   * @return int
   */
  public static function getCurrencyStatus($currencies_id, $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('currencies', ['status' => 1],
        ['currencies_id' => (int)$currencies_id]
      );
    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('currencies', ['status' => 0],
        ['currencies_id' => (int)$currencies_id]
      );
    } else {
      return -1;
    }
  }
}
