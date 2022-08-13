<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Products = Registry::get('Products');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Products->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('ProductsAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Products->getDef('alert_module_install_success'), 'success');

      $CLICSHOPPING_Products->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Products = Registry::get('Products');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_catalog_products']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 1,
          'link' => 'index.php?A&Catalog\Products&Products',
          'image' => 'priceupdate.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_catalog_products'
        ];

        $insert_sql_data = ['parent_id' => 3];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Products->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }

        $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_report_stats_products_viewed']);

        if ($Qcheck->fetch() === false) {

          $sql_data_array = ['sort_order' => 5,
              'link' => 'index.php?A&Catalog\Products&StatsProductsViewed',
              'image' => 'stats_products_viewed.gif',
              'b2b_menu' => 0,
              'access' => 0,
              'app_code' => 'app_report_stats_products_viewed'
          ];

          $insert_sql_data = ['parent_id' => 98];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

          $id = $CLICSHOPPING_Db->lastInsertId();

          $languages = $CLICSHOPPING_Language->getLanguages();

          for ($i = 0, $n = \count($languages); $i < $n; $i++) {

            $language_id = $languages[$i]['id'];

            $sql_data_array = ['label' => $CLICSHOPPING_Products->getDef('title_menu')];

            $insert_sql_data = ['id' => (int)$id,
                'language_id' => (int)$language_id
            ];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
          }
        }

        $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_report_stats_low_stock']);

        if ($Qcheck->fetch() === false) {

          $sql_data_array = ['sort_order' => 5,
              'link' => 'index.php?A&Catalog\Products&StatsProductsLowStock',
              'image' => 'stats_customers.gif',
              'b2b_menu' => 0,
              'access' => 0,
              'app_code' => 'app_report_stats_low_stock'
          ];

          $insert_sql_data = ['parent_id' => 107];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

          $id = $CLICSHOPPING_Db->lastInsertId();

          $languages = $CLICSHOPPING_Language->getLanguages();

          for ($i = 0, $n = \count($languages); $i < $n; $i++) {

            $language_id = $languages[$i]['id'];

            $sql_data_array = ['label' => $CLICSHOPPING_Products->getDef('title_menu')];

            $insert_sql_data = ['id' => (int)$id,
                'language_id' => (int)$language_id
            ];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
          }
        }

        $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_report_stats_products_expected']);

        if ($Qcheck->fetch() === false) {
          $sql_data_array = ['sort_order' => 5,
              'link' => 'index.php?A&Catalog\Products&StatsProductsExpected',
              'image' => 'products_expected.gif',
              'b2b_menu' => 0,
              'access' => 0,
              'app_code' => 'app_report_stats_products_expected'
          ];

          $insert_sql_data = ['parent_id' => 107];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

          $id = $CLICSHOPPING_Db->lastInsertId();

          $languages = $CLICSHOPPING_Language->getLanguages();

          for ($i = 0, $n = \count($languages); $i < $n; $i++) {

            $language_id = $languages[$i]['id'];

            $sql_data_array = ['label' => $CLICSHOPPING_Products->getDef('title_menu')];

            $insert_sql_data = ['id' => (int)$id,
                'language_id' => (int)$language_id
            ];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
          }
        }

        $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_report_stats_products_purchased']);

        if ($Qcheck->fetch() === false) {

          $sql_data_array = ['sort_order' => 5,
              'link' => 'index.php?A&Catalog\Products&StatsProductsPurchased',
              'image' => 'stats_products_purchased.gif',
              'b2b_menu' => 0,
              'access' => 0,
              'app_code' => 'app_report_stats_products_purchased'
          ];

          $insert_sql_data = ['parent_id' => 98];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

          $id = $CLICSHOPPING_Db->lastInsertId();

          $languages = $CLICSHOPPING_Language->getLanguages();

          for ($i = 0, $n = \count($languages); $i < $n; $i++) {

            $language_id = $languages[$i]['id'];

            $sql_data_array = ['label' => $CLICSHOPPING_Products->getDef('title_menu')];

            $insert_sql_data = ['id' => (int)$id,
                'language_id' => (int)$language_id
            ];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
          }
        }

        Cache::clear('menu-administrator');
      }
    }
  }
