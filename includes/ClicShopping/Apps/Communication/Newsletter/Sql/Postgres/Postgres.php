<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\Newsletter\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_Newsletter = Registry::get('Newsletter');
    $CLICSHOPPING_Newsletter->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Newsletter = Registry::get('Newsletter');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_communication_newsletter']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 6,
        'link' => 'index.php?A&Communication\Newsletter&Newsletter',
        'image' => 'newsletters.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_communication_newsletter'
      ];

      $insert_sql_data = ['parent_id' => 6];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Newsletter->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_newsletters')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_newsletters (
  newsletters_id serial PRIMARY KEY,
  title varchar(255) NOT NULL,
  content text NOT NULL,
  module varchar(255) NOT NULL,
  date_added timestamp NOT NULL,
  date_sent timestamp,
  status smallint,
  locked smallint DEFAULT 0,
  languages_id int DEFAULT 0 NOT NULL,
  customers_group_id int DEFAULT 0 NOT NULL,
  newsletters_accept_file smallint DEFAULT 0 NOT NULL,
  newsletters_twitter smallint DEFAULT 0 NOT NULL,
  newsletters_customer_no_account smallint DEFAULT 0 NOT NULL
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_newsletters_customers_temp')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_newsletters_customers_temp (
  customers_firstname varchar(255) NOT NULL,
  customers_lastname varchar(255) NOT NULL,
  customers_email_address varchar(255) NOT NULL
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_newsletters_no_account')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_newsletters_no_account (
  newsletters_id serial PRIMARY KEY,
  customers_firstname varchar(255),
  customers_lastname varchar(255),
  customers_email_address varchar(255) NOT NULL,
  customers_newsletter smallint DEFAULT 1 NOT NULL,
  customers_date_added timestamp,
  languages_id int DEFAULT 1 NOT NULL
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
