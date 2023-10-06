<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_Groups = Registry::get('Groups');
    $CLICSHOPPING_Groups->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Groups = Registry::get('Groups');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Groups->db->get('administrator_menu', 'app_code', ['app_code' => 'app_customers_groups']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 3,
        'link' => 'index.php?A&Customers\Groups&Groups',
        'image' => 'group_client.gif',
        'b2b_menu' => 1,
        'access' => 0,
        'app_code' => 'app_customers_groups'
      ];

      $insert_sql_data = ['parent_id' => 4];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Groups->db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Groups->db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Groups->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Groups->db->save('administrator_menu_description', $sql_data_array);
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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_customers_groups')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_customers_groups (
  customers_group_id serial PRIMARY KEY,
  customers_group_name varchar(32) NOT NULL,
  customers_group_discount decimal(11,2) DEFAULT 0.00 NOT NULL,
  color_bar varchar(8) DEFAULT '#FFFFFF' NOT NULL,
  group_order_taxe smallint DEFAULT 0 NOT NULL,
  group_payment_unallowed varchar(255) DEFAULT 'cc',
  group_shipping_unallowed varchar(255),
  group_tax varchar(5) DEFAULT 'false' NOT NULL,
  customers_group_quantity_default smallint DEFAULT 0 NOT NULL,
  CONSTRAINT idx_customers_group_name UNIQUE (customers_group_name)
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_groups_to_categories')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_groups_to_categories (
      customers_group_id int DEFAULT 0 NOT NULL,
      categories_id int DEFAULT 0 NOT NULL,
      discount decimal(11,2) DEFAULT 0.00 NOT NULL,
      PRIMARY KEY (customers_group_id, categories_id)
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_products_groups')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_products_groups (
      customers_group_id int DEFAULT 0 NOT NULL,
      customers_group_price decimal(15,4) DEFAULT 0.0000 NOT NULL,
      products_id int DEFAULT 0 NOT NULL,
      products_price decimal(15,4) DEFAULT 0.0000 NOT NULL,
      price_group_view char(1) DEFAULT '1' NOT NULL,
      products_group_view char(1) DEFAULT '1' NOT NULL,
      orders_group_view char(1) DEFAULT '1' NOT NULL,
      products_quantity_unit_id_group int DEFAULT 0 NOT NULL,
      products_model_group varchar(255),
      products_quantity_fixed_group int DEFAULT 1 NOT NULL,
      PRIMARY KEY (customers_group_id, products_id),
      CONSTRAINT fk_products_groups_customers_group
        FOREIGN KEY (customers_group_id)
        REFERENCES :table_customers_groups (customers_group_id)
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
