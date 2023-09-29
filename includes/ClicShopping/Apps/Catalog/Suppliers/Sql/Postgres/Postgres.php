<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_ImportExport = Registry::get('Suppliers');
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
    $CLICSHOPPING_Suppliers = Registry::get('Suppliers');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_catalog_suppliers']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 7,
        'link' => 'index.php?A&Catalog\Suppliers&Suppliers',
        'image' => 'suppliers.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_catalog_suppliers'
      ];

      $insert_sql_data = ['parent_id' => 3];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Suppliers->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_suppliers')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_suppliers (
  suppliers_id serial PRIMARY KEY,
  suppliers_name varchar(32) NOT NULL,
  suppliers_image varchar(255),
  date_added timestamp,
  last_modified timestamp,
  suppliers_manager varchar(64),
  suppliers_phone varchar(32),
  suppliers_email_address varchar(96),
  suppliers_fax varchar(32),
  suppliers_address varchar(255),
  suppliers_suburb varchar(255),
  suppliers_postcode varchar(10),
  suppliers_city varchar(32),
  suppliers_states varchar(32),
  suppliers_country_id int NOT NULL DEFAULT 0,
  suppliers_notes text,
  suppliers_status smallint NOT NULL DEFAULT 0,
  CONSTRAINT idx_suppliers_name UNIQUE (suppliers_name)
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_suppliers_info')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_suppliers_info (
  suppliers_id int NOT NULL DEFAULT 0,
  languages_id int NOT NULL DEFAULT 0,
  suppliers_url varchar(255),
  url_clicked smallint NOT NULL DEFAULT 0,
  date_last_click timestamp,
  PRIMARY KEY (suppliers_id, languages_id)
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
