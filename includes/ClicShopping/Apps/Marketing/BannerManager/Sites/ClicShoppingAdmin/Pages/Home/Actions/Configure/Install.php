<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\BannerManager\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_BannerManager = Registry::get('BannerManager');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_BannerManager->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('BannerManagerAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_BannerManager->getDef('alert_module_install_success'), 'success', 'BannerManager');

      $CLICSHOPPING_BannerManager->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_BannerManager = Registry::get('BannerManager');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_banner_manager']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 6,
          'link' => 'index.php?A&Marketing\BannerManager&BannerManager',
          'image' => 'banner_manager.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_marketing_banner_manager'
        ];

        $insert_sql_data = ['parent_id' => 5];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_BannerManager->getDef('title_menu')];

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


    private static function installDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_banners"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_banners (
  banners_id int NOT NULL auto_increment,
  banners_title varchar(255) null,
  banners_url varchar(255) null,
  banners_image varchar(255) null,
  banners_group varchar(255) null,
  banners_target varchar(6) not null,
  banners_html_text text,
  expires_impressions int(7) default(0),
  expires_date datetime,
  date_scheduled datetime,
  date_added datetime not null,
  date_status_change datetime,
  status int(1) default(1) not null,
  languages_id int default(0) not null,
  customers_group_id int default(0) not null,
  banners_title_admin varchar(255) not null,
  PRIMARY KEY banners_id
  KEY idx_banners_group banners_group
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_banners_history"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_banners_history (
  banners_history_id int not null auto_increment,
  banners_id int not null,
  banners_shown int(5) default(0) not null,
  banners_clicked int(5) default(0) not null,
  banners_history_date datetime not null,
  PRIMARY KEY banners_history_id
  KEY idx_banners_history_banners_id (banners_id)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
