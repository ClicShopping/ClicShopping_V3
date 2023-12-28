<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\AdministratorMenu as AdministratorMenuClass;

class Status
{
  protected int $status;
  protected int $id;

  /**
   * @param int $id
   * @param int $status
   * @return int|void status on or off
   *
   */
  public static function getAministratorMenuStatus(int $id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    Registry::set('AdministratorMenuClass', new AdministratorMenuClass());
    $CLICSHOPPING_AdministratorMenuClass = Registry::get('AdministratorMenuClass');

    if ($status == 1) {
      $data = ['id' => $id];

      foreach (array_merge(array($data['id']), $CLICSHOPPING_AdministratorMenuClass->getChildren($data['id'])) as $c) {
        $sql_array = ['status' => 1];
        $update_array = ['id' => (int)$c];

        $CLICSHOPPING_Db->save('administrator_menu', $sql_array, $update_array);
      }

    } elseif ($status == 0) {
      $data = ['id' => $id];

      foreach (array_merge(array($data['id']), $CLICSHOPPING_AdministratorMenuClass->getChildren($data['id'])) as $c) {
        $sql_array = ['status' => 0];
        $update_array = ['id' => (int)$c];

        $CLICSHOPPING_Db->save('administrator_menu', $sql_array, $update_array);
      }
    } else {
      return -1;
    }
  }
}