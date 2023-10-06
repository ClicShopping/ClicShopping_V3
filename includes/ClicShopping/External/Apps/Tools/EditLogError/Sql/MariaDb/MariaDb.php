<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditLogError\Sql\MariaDb;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class MariaDb
{
  public function execute()
  {
    $CLICSHOPPING_EditLogError = Registry::get('EditLogError');
    $CLICSHOPPING_EditLogError->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installMenuAdministration();
  }

/**
* @return void
 */
  private static function installMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_EditLogError = Registry::get('EditLogError');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_tools_edit_log_error']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 5,
        'link' => 'index.php?A&Tools\EditLogError&LogError',
        'image' => 'log.png',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_tools_edit_log_error'
      ];

      $insert_sql_data = ['parent_id' => 178];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_EditLogError->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
      }
//menu 2
      $sql_data_array = [
        'sort_order' => 6,
        'link' => 'index.php?A&Tools\EditLogError&LogErrorPhpMailer',
        'image' => 'log.png',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_tools_edit_log_error'
      ];

      $insert_sql_data = ['parent_id' => 178];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_EditLogError->getDef('title_menu_phpmailer')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
      }

      Cache::clear('menu-administrator');
    }
  }
}