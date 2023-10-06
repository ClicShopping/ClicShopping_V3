<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Sql\MariaDb;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class MariaDb
{
  public function execute()
  {
    $CLICSHOPPING_ProductsQuantityUnit = Registry::get('ProductsQuantityUnit');
    $CLICSHOPPING_ProductsQuantityUnit->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

/**
* @return void
 */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_ProductsQuantityUnit = Registry::get('ProductsQuantityUnit');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_products_quantity_unit']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 8,
        'link' => 'index.php?A&Configuration\ProductsQuantityUnit&ProductsQuantityUnit',
        'image' => 'products_unit.png',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_configuration_products_quantity_unit'
      ];

      $insert_sql_data = ['parent_id' => 13];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_ProductsQuantityUnit->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_products_quantity_unit"');

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_products_quantity_unit (
  products_quantity_unit_id int(11) NOT NULL,
  language_id int(11) NOT NULL,
  products_quantity_unit_title varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_products_quantity_unit
  ADD PRIMARY KEY (products_quantity_unit_id, language_id),
  ADD KEY idx_products_quantity_unit_title (products_quantity_unit_title);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}