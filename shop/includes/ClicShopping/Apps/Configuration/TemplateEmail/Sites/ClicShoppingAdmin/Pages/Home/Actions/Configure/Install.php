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

  namespace ClicShopping\Apps\Configuration\TemplateEmail\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_TemplateEmail = Registry::get('TemplateEmail');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_TemplateEmail->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('TemplateEmailAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsTemplateEmailDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TemplateEmail->getDef('alert_module_install_success'), 'success', 'TemplateEmail');

      $CLICSHOPPING_TemplateEmail->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_TemplateEmail = Registry::get('TemplateEmail');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_template_email']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 3,
                           'link' => 'index.php?A&Configuration\TemplateEmail&TemplateEmail',
                           'image' => 'mail.gif',
                           'b2b_menu' => 0,
                           'access' => 0,
                           'app_code' => 'app_configuration_template_email'
                          ];

        $insert_sql_data = ['parent_id' => 20];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i=0, $n=count($languages); $i<$n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_TemplateEmail->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
                              'language_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array );

        }

        Cache::clear('menu-administrator');
      }
    }


    private function installProductsTemplateEmailDb() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_template_email"');

      if ($Qcheck->fetch() === false) {
$sql = <<<EOD
CREATE TABLE :table_template_email (
  template_email_id int not_null auto_increment,
  template_email_variable varchar(250) not_null,
  customers_group_id int(2) default(0) not_null,
  template_email_type smallint(1) default(0) not_null
  PRIMARY KEY (template_email_id),
  KEY idx_template_email_id (template_email_id)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_template_email_description"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_template_email_description (
  template_email_id int not_null,
  language_id int not_null,
  template_email_name varchar(250),
  template_email_short_description varchar(250),
  template_email_description longtext
  PRIMARY KEY (template_email_id) (language_id),
  KEY idx_template_email_name (idx_template_email_name)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
