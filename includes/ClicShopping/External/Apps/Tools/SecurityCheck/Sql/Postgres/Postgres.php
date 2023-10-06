<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecDirPermissions\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_SecurityCheck = Registry::get('SecurityCheck');
    $CLICSHOPPING_SecurityCheck->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_SecurityCheck = Registry::get('SecurityCheck');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_tools_security_check']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 1,
        'link' => 'index.php?A&Tools\SecurityCheck&SecurityCheck',
        'image' => 'cybermarketing.gif',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_tools_security_check'
      ];

      $insert_sql_data = ['parent_id' => 178];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);
      $id = $CLICSHOPPING_Db->lastInsertId();

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_SecurityCheck->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
      }

      $sql_data_array = [
        'sort_order' => 1,
        'link' => 'index.php?A&Tools\SecurityCheck&IpRestriction',
        'image' => 'cybermarketing.gif',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_tools_security_check'
      ];

      $insert_sql_data = ['parent_id' => 178];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_SecurityCheck->getDef('title_menu_ip_restriction')];

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
  private static function installDb(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_ip_restriction')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_ip_restriction (
      id serial PRIMARY KEY,
      ip_restriction varchar(64) NOT NULL,
      ip_comment varchar(255),
      ip_status_shop smallint default 0 NOT NULL,
      ip_status_admin smallint default 0 NOT NULL
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE :table_ip_restriction_stats (
      id serial PRIMARY KEY,
      ip_remote varchar(64) NOT NULL
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
