<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\Newsletter\Sql\MariaDb;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class MariaDb
{
  /**
   * Executes the installation process for the Newsletter module.
   *
   * @return void
   */
  public function execute()
  {
    $CLICSHOPPING_Newsletter = Registry::get('Newsletter');
    $CLICSHOPPING_Newsletter->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * Installs the database entries required for the Newsletter administration menu.
   *
   * This method checks if the necessary menu entries for the Newsletter application
   * exist in the administrator menu. If not, it creates the entries, including descriptions
   * for all available languages, and clears the administrator menu cache to ensure the new
   * menu is loaded correctly.
   *
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
   * Creates and initializes database tables required for newsletters if they do not already exist.
   *
   * @return void
   */
  private static function installDb()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_newsletters"');

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_newsletters (
  newsletters_id int(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL,
  content text NOT NULL,
  module varchar(255) NOT NULL,
  date_added datetime NOT NULL,
  date_sent datetime,
  status tinyint(1),
  locked tinyint(1) default(0),
  languages_id int(11) default(0) NOT NULL,
  customers_group_id int default(0) NOT NULL,
  newsletters_accept_file int(1) default(0) NOT NULL,
  newsletters_twitter tinyint(1) default(0) NOT NULL,
  newsletters_customer_no_account tinyint(1) default(0) NOT NULL
  PRIMARY KEY newsletters_id
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_newsletters_customers_temp"');

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_newsletters_customers_temp (
customers_firstname varchar(255) NOT NULL,
customers_lastname varchar(255) NOT NULL,
customers_email_address varchar(255) NOT NULL
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_newsletters_no_account"');

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_newsletters_no_account (
  newsletters_id int NOT NULL auto_increment,
  customers_firstname varchar(255) null,
  customers_lastname varchar(255) null,
  customers_email_address varchar(255) NOT NULL,
  customers_newsletter tinyint(1) default(1) NOT NULL,
  customers_date_added datetime,
  languages_id int default(1) NOT NULL
  PRIMARY KEY newsletters_id
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}