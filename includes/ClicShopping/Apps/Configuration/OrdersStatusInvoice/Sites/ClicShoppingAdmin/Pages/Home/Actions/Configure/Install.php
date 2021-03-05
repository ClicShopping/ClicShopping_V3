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

  namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_OrdersStatusInvoice = Registry::get('OrdersStatusInvoice');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_OrdersStatusInvoice->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('OrdersStatusInvoiceAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsOrdersStatusInvoiceDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_OrdersStatusInvoice->getDef('alert_module_install_success'), 'success', 'OrdersStatusInvoice');

      $CLICSHOPPING_OrdersStatusInvoice->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_OrdersStatusInvoice = Registry::get('OrdersStatusInvoice');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_orders_status_invoice']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 7,
          'link' => 'index.php?A&Configuration\OrdersStatusInvoice&OrdersStatusInvoice',
          'image' => 'configuration_26.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_configuration_orders_status_invoice'
        ];

        $insert_sql_data = ['parent_id' => 14];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_OrdersStatusInvoice->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }

        Cache::clear('menu-administrator');
      }
    }


    private static function installProductsOrdersStatusInvoiceDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_orders_status_invoice"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_orders_status_invoice (
  orders_status_invoice_id int default(0) NOT NULL,
  language_id int default(1) NOT NULL,
  orders_status_invoice_name varchar(32) NOT NULL
  PRIMARY KEY (orders_status_invoice_id) language_id,
  KEY idx_orders_status_invoice_name (orders_status_invoice_name)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
