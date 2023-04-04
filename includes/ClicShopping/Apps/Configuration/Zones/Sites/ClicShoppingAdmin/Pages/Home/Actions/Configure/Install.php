<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Zones\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Zones = Registry::get('Zones');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Zones->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('ZonesAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsZonesDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Zones->getDef('alert_module_install_success'), 'success', 'Zones');

      $CLICSHOPPING_Zones->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Zones = Registry::get('Zones');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_zones']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 3,
          'link' => 'index.php?A&Configuration\Zones&Zones',
          'image' => 'zones.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_configuration_zones'
        ];

        $insert_sql_data = ['parent_id' => 19];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Zones->getDef('title_menu')];

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


    private static function installProductsZonesDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_zones"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_zones (
  zone_id int NOT NULL auto_increment,
  zone_country_id int NOT NULL,
  zone_code varchar(255) NOT NULL,
  zone_name varchar(255) NOT NULL,
  zone_status tinyint(1) default(0) NOT NULL
  PRIMARY KEY idx_zones_country_id (zone_country_id)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
