<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxRates\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_TaxRates = Registry::get('TaxRates');
    $CLICSHOPPING_TaxRates->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_TaxRates = Registry::get('TaxRates');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_tax_rates']);

    if ($Qcheck->fetch() === false) {

      $sql_data_array = ['sort_order' => 4,
        'link' => 'index.php?A&Configuration\TaxRates&TaxRates',
        'image' => 'tax_rates.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_configuration_tax_rates'
      ];

      $insert_sql_data = ['parent_id' => 19];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_TaxRates->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_tax_rates')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_tax_rates (
  tax_rates_id serial PRIMARY KEY,
  tax_zone_id int NOT NULL,
  tax_class_id int NOT NULL,
  tax_priority int DEFAULT 1,
  tax_rate decimal(7,4) NOT NULL,
  tax_description varchar(255) NOT NULL,
  last_modified timestamp,
  date_added timestamp NOT NULL,
  code_tax_erp varchar(15)
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}