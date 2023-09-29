<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sql\MariaDb;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class MariaDb
{
  public function execute()
  {
    $CLICSHOPPING_ImportExport = Registry::get('ProductsAttributes');
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
    $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_catalog_products_attributes']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 7,
        'link' => 'index.php?A&Catalog\ProductsAttributes&ProductsAttributes',
        'image' => 'products_option.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_catalog_products_attributes'
      ];

      $insert_sql_data = ['parent_id' => 3];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_ProductsAttributes->getDef('title_menu')];

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
  private static function updateSQL(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcheckField = $CLICSHOPPING_Db->query("show columns from :table_products_attributes like 'status'");

    if ($QcheckField->fetch() === false) {
      $sql = <<<EOD
ALTER TABLE :table_products_attributes ADD status TINYINT(1) NOT NULL DEFAULT '1' AFTER `products_attributes_image`;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}