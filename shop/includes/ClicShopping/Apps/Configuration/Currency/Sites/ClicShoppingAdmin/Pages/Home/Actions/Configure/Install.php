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

  namespace ClicShopping\Apps\Configuration\Currency\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Currency = Registry::get('Currency');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Currency->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('CurrencyAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installCurrencyDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Currency->getDef('alert_module_install_success'), 'success', 'Currency');

      $CLICSHOPPING_Currency->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Currency = Registry::get('Currency');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_currency']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 1,
                           'link' => 'index.php?A&Configuration\Currency&Currency',
                           'image' => 'currencies.gif',
                           'b2b_menu' => 0,
                           'access' => 0,
                           'app_code' => 'app_configuration_currency'
                          ];

        $insert_sql_data = ['parent_id' => 20];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i=0, $n=count($languages); $i<$n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Currency->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
                              'language_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array );

        }

        Cache::clear('menu-administrator');
      }
    }

    private function installCurrencyDb() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_currencies"');

      if ($Qcheck->fetch() === false) {
$sql = <<<EOD
CREATE TABLE :table_currencies (
  currencies_id int not_null auto_increment,
  title varchar(255) not_null,
  code char(3) not_null,
  symbol_left varchar(12)
  symbol_right varchar(12),
  decimal_point char(1),
  thousands_point char(1),
  decimal_places char(1),
  value float(13,8),
  last_updated datetime,
  PRIMARY KEY currencies_id,
  KEY idx_currencies_id(code code)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
