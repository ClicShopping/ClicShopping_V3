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

  namespace ClicShopping\Apps\Marketing\SEO\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_SEO = Registry::get('SEO');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_SEO->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('SEOAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_SEO->getDef('alert_module_install_success'), 'success', 'SEO');

      $CLICSHOPPING_SEO->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_SEO = Registry::get('SEO');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_seo']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 7,
          'link' => 'index.php?A&Marketing\SEO&SEO',
          'image' => 'referencement.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_marketing_seo'
        ];

        $insert_sql_data = ['parent_id' => 5];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_SEO->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }

        Cache::clear('menu-administrator');
      }
    }

    private function installDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_submit_description"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_submit_description (
submit_id int NOT NULL defautl(1),
language_id int NOT NULL defautl(1),
submit_defaut_language_title varchar(255),
submit_defaut_language_keywords text,
submit_defaut_language_description varchar(255),
submit_defaut_language_footer text,
submit_language_products_info_title varchar(255),
submit_language_products_info_keywords text,
submit_language_products_info_description varchar(255),
submit_language_products_new_title varchar(255),
submit_language_products_new_keywords text,
submit_language_products_new_description varchar(255),
submit_language_special_title varchar(255),
submit_language_special_keywords text,
submit_language_special_description varchar(255),
submit_language_reviews_title varchar(255),
submit_language_reviews_keywords text,
submit_language_reviews_description varchar(255)
  PRIMARY KEY submit_id (language_id),
  KEY idx_seo_submit_id
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
