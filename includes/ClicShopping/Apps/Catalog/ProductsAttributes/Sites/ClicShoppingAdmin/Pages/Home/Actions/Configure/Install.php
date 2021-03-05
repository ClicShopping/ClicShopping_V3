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

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_ProductsAttributes->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('ProductsAttributesAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::updateSQL();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ProductsAttributes->getDef('alert_module_install_success'), 'success', 'ProductsAttributes');

      $CLICSHOPPING_ProductsAttributes->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_catalog_products_attributes']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 7,
          'link' => 'index.php?A&Catalog\ProductsAttributes&ProductsAttributes',
          'image' => 'products_option.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_catalog_products_attributes'
        ];

        $insert_sql_data = ['parent_id' => 3];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_ProductsAttributes->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }

    private static function updateSQL()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $QcheckField = $CLICSHOPPING_Db->query("show columns from :table_products_attributes like 'status'");

      if ($QcheckField->fetch() === false) {
        $sql = <<<EOD
ALTER TABLE :table_products_attributes ADD status TINYINT(1) NOT NULL DEFAULT '1' AFTER `products_attributes_image`;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
