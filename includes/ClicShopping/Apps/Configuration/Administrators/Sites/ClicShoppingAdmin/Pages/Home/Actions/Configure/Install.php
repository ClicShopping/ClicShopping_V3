<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Administrators\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Administrators = Registry::get('Administrators');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Administrators->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('AdministratorsAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsAdministratorsDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Administrators->getDef('alert_module_install_success'), 'success', 'Administrators');

      $CLICSHOPPING_Administrators->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Administrators = Registry::get('Administrators');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_administrators']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 1,
          'link' => 'index.php?A&Configuration\Administrators&Administrators',
          'image' => 'administrators.gif',
          'b2b_menu' => 0,
          'access' => 1,
          'app_code' => 'app_configuration_administrators'
        ];

        $insert_sql_data = ['parent_id' => 14];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Administrators->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }

        Cache::clear('menu-administrator');
      }
    }

    private static function installProductsAdministratorsDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_administrators"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_administrators (
  id int NOT NULL auto_increment,
  user_name varchar(255) binary NOT NULL,
  user_password varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  first_name varchar(255) NOT NULL,
  access tinyint(1) NOT NULL default(0)
  PRIMARY KEY id
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
