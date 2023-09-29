<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Favorites\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_Favorites = Registry::get('Favorites');
    $CLICSHOPPING_Favorites->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Favorites = Registry::get('Favorites');
    $CLICSHOPPING_Language = Registry::get('Language');
    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_favorites']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 1,
        'link' => 'index.php?A&Marketing\Favorites&Favorites',
        'image' => 'products_favorites.png',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_marketing_favorites'
      ];

      $insert_sql_data = ['parent_id' => 5];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Favorites->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
      }

      $sql_data_array = [
        'sort_order' => 1,
        'link' => 'index.php?A&Marketing\Favorites&Favorites',
        'link' => 'index.php?A&Configuration\Modules&Modules&set=modules_products_favorites',
        'image' => 'products_favorites.png',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_marketing_favorites'
      ];

      $insert_sql_data = ['parent_id' => 117];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Favorites->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_products_favorites')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_products_favorites (
      products_favorites_id serial PRIMARY KEY,
      products_id int NOT NULL default(0),
      products_favorites_date_added timestamp,
      products_favorites_last_modified timestamp,
      scheduled_date timestamp,
      expires_date timestamp,
      date_status_change timestamp,
      status smallint NOT NULL default(1),
      customers_group_id int NOT NULL default(0)
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE INDEX idx_products_favorites_id ON :table_products_favorites (products_id);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
