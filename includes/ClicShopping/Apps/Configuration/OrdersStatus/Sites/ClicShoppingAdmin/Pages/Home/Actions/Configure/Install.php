<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\OrdersStatus\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_OrdersStatus = Registry::get('OrdersStatus');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_OrdersStatus->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('OrdersStatusAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsOrdersStatusDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_OrdersStatus->getDef('alert_module_install_success'), 'success', 'OrdersStatus');

      $CLICSHOPPING_OrdersStatus->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_OrdersStatus = Registry::get('OrdersStatus');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_orders_status']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 6,
          'link' => 'index.php?A&Configuration\OrdersStatus&OrdersStatus',
          'image' => 'order_status.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_configuration_orders_status'
        ];

        $insert_sql_data = ['parent_id' => 14];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_OrdersStatus->getDef('title_menu')];

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


    private static function installProductsOrdersStatusDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_orders_status"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_orders_status (
  orders_status_id int default(0) NOT NULL,
  language_id int default(1) NOT NULL,
  orders_status_name varchar(255) NOT NULL,
  public_flag tinyint(1) default(1),
  downloads_flag tinyint(1) default(0),
  support_orders_flag int(1) default(0),
  authorize_to_delete_order tinyint(1) default(1)  
  PRIMARY KEY (orders_status_id) language_id,
  KEY idx_orders_status_name (orders_status_name)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
