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

  namespace ClicShopping\Apps\Payment\PayPal\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $this->app = Registry::get('PayPal');

      $current_module = $this->page->data['current_module'];

      $m = Registry::get('PayPalAdminConfig' . $current_module);
      $m->install();

      $this->installDb();
      static::installDbMenuAdministration();

      $CLICSHOPPING_MessageStack->add($this->app->getDef('alert_module_install_success'), 'success', 'PayPal');

      $this->app->redirect('Configure&module=' . $current_module);
    }

    private function installDb()
    {

      $Qcheck = $this->app->db->query('show tables like ":table_clicshopping_app_paypal_log"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
  CREATE TABLE :table_clicshopping_app_paypal_log (
    id int unsigned NOT NULL auto_increment,
    customers_id int NOT NULL,
    module varchar(8) NOT NULL,
    action varchar(255) NOT NULL,
    result tinyint NOT NULL,
    server tinyint NOT NULL,
    request text NOT NULL,
    response text NOT NULL,
    ip_address int unsigned,
    date_added datetime,
    PRIMARY KEY (id),
    KEY idx_oapl_module (module)
  ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOD;
        $this->app->db->exec($sql);
      }
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_payment_paypal']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 4,
          'link' => 'index.php?A&Payment\PayPal&Configure',
          'image' => 'modules_payment.gif',
          'b2b_menu' => 0,
          'access' => 1,
          'app_code' => 'app_payment_paypal'
        ];

        $insert_sql_data = ['parent_id' => 186];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => 'PayPal'];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }

        Cache::clear('menu-administrator');
      }
    }
  }
