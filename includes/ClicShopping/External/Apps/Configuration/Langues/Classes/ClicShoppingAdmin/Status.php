<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class Status
{
  /**
   * Status language -  Sets the status of a language
   * @param int $languages_id
   * @param int $status
   * @return int
   */
  public static function getLanguageStatus(int $languages_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status === 1) {
      return $CLICSHOPPING_Db->save('languages', ['status' => 1], ['languages_id' => (int)$languages_id]);
    } elseif ($status === 0) {
      $Qcheck = $CLICSHOPPING_Db->prepare('select code
                                             from :table_languages
                                             where languages_id = :languages_id
                                            ');
      $Qcheck->bindValue(':languages_id', $languages_id);
      $Qcheck->execute();

      if ($Qcheck->value('code') == DEFAULT_LANGUAGE && $Qcheck->value('code') != 'en') {
        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_configuration
                                                set configuration_value = :configuration_value
                                                where configuration_key = :configuration_key
                                               ');
        $Qupdate->bindValue(':configuration_value', 'en');
        $Qupdate->bindValue(':configuration_key', 'DEFAULT_LANGUAGE');

        $Qupdate->execute();
      }

      return $CLICSHOPPING_Db->save('languages', ['status' => 0], ['languages_id' => (int)$languages_id]);
    } else {
      return -1;
    }
  }
}
