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

  namespace ClicShopping\Apps\Configuration\Countries\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;

  class Status {

    protected $countries_id;
    protected $status;

    Public static function getCountriesStatus($countries_id, $status) {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {
        return $CLICSHOPPING_Db->save('countries', ['status' => 1],
                                            ['countries_id' => (int)$countries_id]
                              );

      } elseif ($status == 0) {

        return $CLICSHOPPING_Db->save('countries', ['status' => 0],
                                            ['countries_id' => (int)$countries_id]
                              );

      } else {
        return -1;
      }
    }
  }
