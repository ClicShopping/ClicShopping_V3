<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{
  /**
   * @param int $id
   * @param int $status
   * @return int
   */
  public static function getAdministratorStatus(int $id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == 1) {
      return $CLICSHOPPING_Db->save('administrators', [
        'status' => 1,
        'last_modified' => 'now()'
      ],
        ['id' => (int)$id]
      );

    } elseif ($status == 0) {
      return $CLICSHOPPING_Db->save('administrators', [
        'status' => 0,
        'last_modified' => 'now()'
      ],
        ['id' => (int)$id]
      );

    } else {
      return -1;
    }
  }
}