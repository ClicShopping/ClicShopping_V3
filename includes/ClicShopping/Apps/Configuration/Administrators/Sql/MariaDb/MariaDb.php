<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Administrators\Sql\MariaDb;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class MariaDb
{
  public function execute()
  {
    $CLICSHOPPING_ImportExport = Registry::get('Administrators');
    $CLICSHOPPING_ImportExport->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

/**
* @return void
 */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Administrators = Registry::get('Administrators');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_administrators']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 1,
        'link' => 'index.php?A&Configuration\Administrators&Administrators',
        'image' => 'administrators.gif',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_configuration_administrators'
      ];

      $insert_sql_data = ['parent_id' => 14];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Administrators->getDef('title_menu')];

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

/**
* @return void
 */
  private static function installDb()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_administrators"');

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_administrators (
  id int NOT NULL auto_increment,
  user_name varchar(255) binary NOT NULL,
  user_password varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  first_name varchar(255) NOT NULL,
  access tinyint(1) NOT NULL default(0)
  PRIMARY KEY id
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}