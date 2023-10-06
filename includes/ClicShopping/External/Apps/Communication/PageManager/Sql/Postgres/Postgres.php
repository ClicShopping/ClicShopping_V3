<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_ImportExport = Registry::get('PageManager');
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
    $CLICSHOPPING_PageManager = Registry::get('PageManager');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_communication_page_manager']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 0,
        'link' => 'index.php?A&Communication\PageManager&PageManager',
        'image' => 'page_manager.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_communication_page_manager'
      ];

      $insert_sql_data = ['parent_id' => 6];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_PageManager->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_pages_manager')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_pages_manager (
  pages_id serial PRIMARY KEY,
  links_target varchar(6) DEFAULT '_self' NOT NULL,
  sort_order int,
  status smallint DEFAULT 1 NOT NULL,
  page_type smallint DEFAULT 0 NOT NULL,
  page_box smallint DEFAULT 0 NOT NULL,
  page_time varchar(4) NOT NULL,
  page_date_start timestamp,
  page_date_closed timestamp,
  date_added timestamp NOT NULL,
  last_modified timestamp,
  date_status_change timestamp,
  customers_group_id int DEFAULT 0 NOT NULL,
  page_general_condition smallint DEFAULT 0 NOT NULL
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_pages_manager_description')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_pages_manager_description (
  pages_id int DEFAULT 0 NOT NULL,
  pages_title varchar(255) NOT NULL,
  pages_html_text text,
  externallink varchar(255),
  language_id int DEFAULT 1 NOT NULL,
  page_manager_head_title_tag varchar(255),
  page_manager_head_desc_tag varchar(255),
  page_manager_head_keywords_tag varchar(255),
  PRIMARY KEY (pages_id, language_id),
  CONSTRAINT idx_pages_title UNIQUE (pages_title)
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
