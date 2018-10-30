<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_AdministratorMenu->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('AdministratorMenuAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsAdministratorMenuDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_AdministratorMenu->getDef('alert_module_install_success'), 'success', 'AdministratorMenu');

      $CLICSHOPPING_AdministratorMenu->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_AdministratorMenu = Registry::get('AdministratorMenu');
      $CLICSHOPPING_Language = Registry::get('Language');
      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_administrator_menu']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 1,
                           'link' => 'index.php?A&Tools\AdministratorMenu&AdministratorMenu',
                           'image' => 'menu.gif',
                           'b2b_menu' => 0,
                           'access' => 1,
                           'app_code' => 'app_configuration_administrator_menu'
                          ];

        $insert_sql_data = ['parent_id' => 170];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i=0, $n=count($languages); $i<$n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_AdministratorMenu->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
                              'language_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array );

        }

        Cache::clear('menu-administrator');
      }
    }


    private function installProductsAdministratorMenuDb() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_administrator_menu"');

      if ($Qcheck->fetch() === false) {
$sql = <<<EOD
CREATE TABLE :table_administrator_menu (
  administrator_menu_id int not_null auto_increment,
  administrator_menu_variable varchar(250) not_null,
  customers_group_id int(2) default(0) not_null,
  administrator_menu_type smallint(1) default(0) not_null
  PRIMARY KEY (administrator_menu_id),
  KEY idx_administrator_menu_id (administrator_menu_id)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_administrator_menu_description"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_administrator_menu_description (
  administrator_menu_id int not_null,
  language_id int not_null,
  administrator_menu_name varchar(250),
  administrator_menu_short_description varchar(250),
  administrator_menu_description longtext
  PRIMARY KEY (administrator_menu_id) (language_id),
  KEY idx_administrator_menu_name (idx_administrator_menu_name)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
