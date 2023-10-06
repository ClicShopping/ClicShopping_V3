<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TemplateEmail\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_TemplateEmail = Registry::get('TemplateEmail');
    $CLICSHOPPING_TemplateEmail->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_TemplateEmail = Registry::get('TemplateEmail');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_template_email']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 3,
        'link' => 'index.php?A&Configuration\TemplateEmail&TemplateEmail',
        'image' => 'mail.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_configuration_template_email'
      ];

      $insert_sql_data = ['parent_id' => 20];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_TemplateEmail->getDef('title_menu')];

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

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_template_email')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_template_email (
  template_email_id serial PRIMARY KEY,
  template_email_variable varchar(250) NOT NULL,
  customers_group_id smallint DEFAULT 0 NOT NULL,
  template_email_type smallint DEFAULT 0 NOT NULL
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_template_email_description')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
CREATE TABLE :table_template_email_description (
  template_email_id int NOT NULL,
  language_id int NOT NULL,
  template_email_name varchar(250),
  template_email_short_description varchar(250),
  template_email_description text,
  PRIMARY KEY (template_email_id, language_id),
  CONSTRAINT idx_template_email_name UNIQUE (template_email_name)
);
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
