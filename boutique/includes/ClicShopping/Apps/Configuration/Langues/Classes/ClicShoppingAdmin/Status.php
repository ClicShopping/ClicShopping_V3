<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;

  class Status {

    protected $languages_id;
    protected $status;


/*
* Status language -  Sets the status of a language
*
* @param string languages_id, status
* @return string status on or off
* @access public
* osc_set_language_status
*/

    Public static function getLanguageStatus($languages_id, $status) {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {
        return $CLICSHOPPING_Db->save('languages', ['status' => 1],
                                            ['languages_id' => (int)$languages_id]
                              );

      } elseif ($status == 0) {

        return $CLICSHOPPING_Db->save('languages', ['status' => 0],
                                            ['languages_id' => (int)$languages_id]
                              );

      } else {
        return -1;
      }
    }
  }
