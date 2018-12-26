<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\TaxClass\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_TaxClass = Registry::get('TaxClass');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_TaxClass->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('TaxClassAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsTaxClassDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TaxClass->getDef('alert_module_install_success'), 'success', 'TaxClass');

      $CLICSHOPPING_TaxClass->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_TaxClass = Registry::get('TaxClass');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_tax_class']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 2,
                           'link' => 'index.php?A&Configuration\TaxClass&TaxClass',
                           'image' => 'tax_classes.gif',
                           'b2b_menu' => 0,
                           'access' => 0,
                           'app_code' => 'app_configuration_tax_class'
                          ];

        $insert_sql_data = ['parent_id' => 19];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i=0, $n=count($languages); $i<$n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_TaxClass->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
                              'language_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array );

        }

        Cache::clear('menu-administrator');
      }
    }


    private function installProductsTaxClassDb() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_tax_class"');

      if ($Qcheck->fetch() === false) {
$sql = <<<EOD
CREATE TABLE :table_tax_class (
  tax_class_id int not_null auto_increment,
  tax_class_title varchar(32) not_null,
  tax_class_description varchar(255) not_null,
  last_modified datetime,
  date_added datetime not_null
  PRIMARY KEY tax_class_id,
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
