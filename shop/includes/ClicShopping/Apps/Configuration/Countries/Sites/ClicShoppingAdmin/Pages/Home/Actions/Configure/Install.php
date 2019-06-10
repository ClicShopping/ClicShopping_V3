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

  namespace ClicShopping\Apps\Configuration\Countries\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Countries = Registry::get('Countries');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Countries->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('CountriesAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installCountriesDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Countries->getDef('alert_module_install_success'), 'success', 'Countries');

      $CLICSHOPPING_Countries->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Countries = Registry::get('Countries');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_countries']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 1,
          'link' => 'index.php?A&Configuration\Countries&Countries',
          'image' => 'countries.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_configuration_countries'
        ];

        $insert_sql_data = ['parent_id' => 19];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Countries->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }

        Cache::clear('menu-administrator');
      }
    }


    private function installCountriesDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_countries"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_countries (
  countries_id int not_null auto_increment,
  countries_name varchar(255) not_null,
  countries_iso_code_2 char(2) not_null,
  countries_iso_code_3 char(3) not_null,
  address_format_id int not_null,
  status tinyint(1) default(1)
  PRIMARY KEY countries_id,
  KEY idx_countries_name (countries_name)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
