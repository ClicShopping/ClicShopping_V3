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

  namespace ClicShopping\Apps\Catalog\Suppliers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Suppliers = Registry::get('Suppliers');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Suppliers->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('SuppliersAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Suppliers->getDef('alert_module_install_success'), 'success', 'Suppliers');

      $CLICSHOPPING_Suppliers->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Suppliers = Registry::get('Suppliers');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_catalog_suppliers']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 7,
          'link' => 'index.php?A&Catalog\Suppliers&Suppliers',
          'image' => 'suppliers.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_catalog_suppliers'
        ];

        $insert_sql_data = ['parent_id' => 3];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Suppliers->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }

    private static function installDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_suppliers"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_suppliers (
  suppliers_id int not_null auto_increment,
  suppliers_name varchar(32) NOT NULL,
  suppliers_image varchar(255),
  date_added datetime,
  last_modified datetime,
  suppliers_manager varchar(64),
  suppliers_phone varchar(32),
  suppliers_email_address varchar(96),
  suppliers_fax varchar(32),
  suppliers_address varchar(64),
  suppliers_suburb varchar(32),
  suppliers_postcode varchar(10),
  suppliers_city varchar(32),
  suppliers_states varchar(32),
  suppliers_country_id int NOT NULL DEFAULT 0,
  suppliers_notes text,
  suppliers_status int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (suppliers_id),
  ADD KEY idx_suppliers_name (suppliers_name)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }


      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_suppliers_info"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_suppliers_info (
  suppliers_id int NOT NULL DEFAULT 0,
  languages_id int NOT NULL DEFAULT 0,
  suppliers_url varchar(255),
  url_clicked int(5) NOT NULL DEFAULT 0
  date_last_click datetime
  PRIMARY KEY suppliers_id (languages_id)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
