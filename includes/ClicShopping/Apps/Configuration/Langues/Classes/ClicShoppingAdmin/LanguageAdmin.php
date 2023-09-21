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

class LanguageAdmin
{
  /**
   * @return int
   */
  public static function getLatestLanguageID(): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->prepare('select languages_id
                                           from :table_languages
                                           order by languages_id desc
                                           limit 1
                                        ');
    $Qcheck->execute();

    $language_id = $Qcheck->valueInt('languages_id');

    return $language_id;
  }
}