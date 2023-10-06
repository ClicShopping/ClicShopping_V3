<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_Api = Registry::get('Api');
    $CLICSHOPPING_Api->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
  * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Api = Registry::get('Api');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_api']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 14,
        'link' => 'index.php?A&Configuration\Api&Api',
        'image' => 'api.png',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_configuration_api'
      ];

      $insert_sql_data = ['parent_id' => 14];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Api->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_api')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_api (
  api_id serial PRIMARY KEY,
  username varchar(64) NOT NULL,
  api_key text NOT NULL,
  status smallint NOT NULL,
  date_added timestamp NOT NULL,
  date_modified timestamp NOT NULL,
  get_product_status smallint NOT NULL DEFAULT 0,
  update_product_status smallint NOT NULL DEFAULT 0,
  insert_product_status smallint NOT NULL DEFAULT 0,
  delete_product_status smallint NOT NULL DEFAULT 0,
  get_categories_status smallint NOT NULL DEFAULT 0,
  update_categories_status smallint NOT NULL DEFAULT 0,
  insert_categories_status smallint NOT NULL DEFAULT 0,
  delete_categories_status smallint NOT NULL DEFAULT 0,
  get_customer_status smallint NOT NULL DEFAULT 0,
  update_customer_status smallint NOT NULL DEFAULT 0,
  insert_customer_status smallint NOT NULL DEFAULT 0,
  delete_customer_status smallint NOT NULL DEFAULT 0,
  get_order_status smallint NOT NULL DEFAULT 0,
  update_order_status smallint NOT NULL DEFAULT 0,
  insert_order_status smallint NOT NULL DEFAULT 0,
  delete_order_status smallint NOT NULL DEFAULT 0,
  get_manufacturer_status smallint NOT NULL DEFAULT 0,
  update_manufacturer_status smallint NOT NULL DEFAULT 0,
  insert_manufacturer_status smallint NOT NULL DEFAULT 0,
  delete_manufacturer_status smallint NOT NULL DEFAULT 0,
  get_supplier_status smallint NOT NULL DEFAULT 0,
  update_supplier_status smallint NOT NULL DEFAULT 0,
  insert_supplier_status smallint NOT NULL DEFAULT 0,
  delete_supplier_status smallint NOT NULL DEFAULT 0
);

INSERT INTO :table_api VALUES(1, 'Default', 'd0a36b839700b60727fe13998e22aa0af197c61d8b371e26114c133ca51c4864bd0da73ad6d1e5090b02b55cff42b8a0cd23866e64e78fc8884eb6228d32f5e9d76bed468869dd89ee6bb8a3208c5077e88560d0bc238f67cfc732efcf5313a0cb361e297c29c8d82d050d770ed7dee972af6445e801fa9af12e3d478bf5346a', 1, '2022-09-18 14:25:54', '2022-09-18 14:25:54');
      

 CREATE TABLE :table_api_ip (
  api_ip_id serial PRIMARY KEY,
  api_id int NOT NULL,
  ip varchar(40) NOT NULL
);

CREATE TABLE :table_api_session (
  api_session_id serial PRIMARY KEY,
  api_id int NOT NULL,
  session_id varchar(32) NOT NULL,
  ip varchar(40) NOT NULL,
  date_added timestamp NOT NULL,
  date_modified timestamp NOT NULL
);

EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
