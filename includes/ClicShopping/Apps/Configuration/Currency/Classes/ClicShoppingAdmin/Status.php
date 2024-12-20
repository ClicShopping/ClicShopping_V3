<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Currency\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{
  /**
   * Updates the currency status in the database based on the given status.
   *
   * @param int $currencies_id The ID of the currency to update.
   * @param int $status The status to set for the currency (1 for active, 0 for inactive).
   * @return mixed Returns the result of the database save operation, or -1 if the status is invalid.
   */
  public static function getCurrencyStatus(int $currencies_id, int $status)
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
