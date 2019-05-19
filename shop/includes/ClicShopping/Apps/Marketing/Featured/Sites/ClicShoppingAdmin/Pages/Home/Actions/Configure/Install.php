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

  namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Featured = Registry::get('Featured');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Featured->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('FeaturedAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsFeaturedDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Featured->getDef('alert_module_install_success'), 'success', 'Featured');

      $CLICSHOPPING_Featured->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Featured = Registry::get('Featured');
      $CLICSHOPPING_Language = Registry::get('Language');
      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_featured']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 1,
          'link' => 'index.php?A&Marketing\Featured&Featured',
          'image' => 'products_featured.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_marketing_featured'
        ];

        $insert_sql_data = ['parent_id' => 5];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Featured->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        $sql_data_array = ['sort_order' => 1,
          'link' => 'index.php?A&Marketing\Featured&Featured',
          'link' => 'index.php?A&Configuration\Modules&Modules&set=modules_products_featured',
          'image' => 'products_featured.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_marketing_featured'
        ];

        $insert_sql_data = ['parent_id' => 117];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Featured->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }

    private function installProductsFeaturedDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_products_featured"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_products_featured (
  products_featured_id int(11) not_null,
  products_id int not_null default(0),
  products_featured_date_added datetime,
  products_featured_last_modified datetime,
  scheduled_date datetime,
  expires_date datetime,
  date_status_change datetime,
  status tinyint(1) not_null default(1),
  customers_group_id int not_null default(0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE :table_products_featured ADD PRIMARY KEY (products_featured_id),  ADD KEY idx_products_featured_id (products_id);
ALTER TABLE :table_products_featured MODIFY products_featured_id int(11) NOT NULL AUTO_INCREMENT;
EOD;

        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
