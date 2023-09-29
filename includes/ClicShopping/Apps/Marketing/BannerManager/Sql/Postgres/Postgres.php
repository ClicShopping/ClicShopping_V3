<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\BannerManager\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_BannerManager = Registry::get('BannerManager');
    $CLICSHOPPING_BannerManager->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_BannerManager = Registry::get('BannerManager');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_banner_manager']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 6,
        'link' => 'index.php?A&Marketing\BannerManager&BannerManager',
        'image' => 'banner_manager.png',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_marketing_banner_manager'
      ];

      $insert_sql_data = ['parent_id' => 5];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_BannerManager->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_banners')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_banners (
      banners_id serial PRIMARY KEY,
      banners_title varchar(255),
      banners_url varchar(255),
      banners_image varchar(255),
      banners_group varchar(255),
      banners_target varchar(6) NOT NULL,
      banners_html_text text,
      expires_impressions int default(0),
      expires_date timestamp,
      date_scheduled timestamp,
      date_added timestamp NOT NULL,
      date_status_change timestamp,
      status int default(1) NOT NULL,
      languages_id int default(0) NOT NULL,
      customers_group_id int default(0) NOT NULL,
      banners_title_admin varchar(255) NOT NULL
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_banners_history')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_banners_history (
      banners_history_id serial PRIMARY KEY,
      banners_id int NOT NULL,
      banners_shown int default(0) NOT NULL,
      banners_clicked int default(0) NOT NULL,
      banners_history_date timestamp NOT NULL
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
