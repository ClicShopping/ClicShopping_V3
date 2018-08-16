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

  namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Reviews->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('ReviewsAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Reviews->getDef('alert_module_install_success'), 'success', 'reviews');

      $CLICSHOPPING_Reviews->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() {
      $CLICSHOPPING_Reviews = Registry::get('Reviews');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Reviews->db->get('administrator_menu', 'app_code', ['app_code' => 'app_customers_reviews']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 6,
                           'link' => 'index.php?A&Customers\Reviews&Reviews',
                           'image' => 'reviews.gif',
                           'b2b_menu' => 0,
                           'access' => 0,
                           'app_code' => 'app_customers_reviews'
                          ];

        $insert_sql_data = ['parent_id' => 4];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Reviews->db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Reviews->db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i=0, $n=count($languages); $i<$n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Reviews->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
                              'language_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Reviews->db->save('administrator_menu_description', $sql_data_array );
        }

        Cache::clear('menu-administrator');
      }
    }

    private static function installDb() {
      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      $Qcheck = $CLICSHOPPING_Reviews->db->query('show tables like ":table_reviews"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_reviews (
  reviews_id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL,
  customers_id int(11) DEFAULT NULL,
  customers_name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  reviews_rating int(1) DEFAULT NULL,
  date_added datetime DEFAULT NULL,
  last_modified datetime DEFAULT NULL,
  reviews_read int(5) NOT NULL DEFAULT (0),
  status tinyint(1) NOT NULL DEFAULT (0),
  customers_group_id int(11) NOT NULL DEFAULT (0)
  PRIMARY KEY (reviews_id),
  KEY idx_reviews_products_id (products_id)
      idx_reviews_customers_id (customers_id)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
        $CLICSHOPPING_Reviews->db->exec($sql);

        $Qcheck = $CLICSHOPPING_Reviews->db->query('show tables like ":table_reviews_description"');

        if ($Qcheck->fetch() === false) {
          $sql = <<<EOD
CREATE TABLE :table_reviews_description (
  reviews_id int(11) NOT NULL,
  languages_id int(11) NOT NULL,
  reviews_text text COLLATE utf8_unicode_ci NOT NULL
  PRIMARY KEY (reviews_id, languages_id)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOD;
          $CLICSHOPPING_Reviews->db->exec($sql);
        }
      }
    }
  }
