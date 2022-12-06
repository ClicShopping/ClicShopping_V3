<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Api\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class Status
  {
   /**
     * @param int $is
     * @param int $status
     * @return int
     */
    Public static function getApiStatus(int $id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == '1') {
        $update_array = [
          'status' => 1,
          'date_modified' => 'now()'
        ];

        return $CLICSHOPPING_Db->save('api',  $update_array, ['api_id' => (int)$id]);
      } elseif ($status == '0') {
        $update_array = [
          'status' => 0,
          'date_modified' => 'now()',
         ];

        return $CLICSHOPPING_Db->save('api', $update_array, ['api_id' => (int)$id]);
      } else {
        return -1;
      }
    }
  }