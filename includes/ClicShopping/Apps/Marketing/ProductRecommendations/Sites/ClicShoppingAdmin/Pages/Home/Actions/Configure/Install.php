<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\ProductRecommendations\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ProductRecommendations = Registry::get('ProductRecommendations');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_ProductRecommendations->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('ProductRecommendationsAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsProductRecommendationsDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ProductRecommendations->getDef('alert_module_install_success'), 'success');

      $CLICSHOPPING_ProductRecommendations->redirect('Configure&module=' . $current_module);
    }

    /**
     *
     */
    private static function installDbMenuAdministration() :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_ProductRecommendations = Registry::get('ProductRecommendations');
      $CLICSHOPPING_Language = Registry::get('Language');
      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_product_recommandations']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = [
          'sort_order' => 1,
          'link' => 'index.php?A&Marketing\ProductRecommendations&ProductRecommendations',
          'image' => 'products_recommendations.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_marketing_product_recommandations'
        ];

        $insert_sql_data = ['parent_id' => 107];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_ProductRecommendations->getDef('title_menu')];

          $insert_sql_data = [
            'id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        $sql_data_array = [
          'sort_order' => 1,
          'link' => 'index.php?A&Marketing\ProductRecommendations&ProductRecommendations',
          'link' => 'index.php?A&Configuration\Modules&Modules&set=modules_products_recommendations',
          'image' => 'products_recommendations.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_marketing_product_recommandations'
        ];

        $insert_sql_data = ['parent_id' => 117];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_ProductRecommendations->getDef('title_menu')];

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

    /**
     *
     */
    private static function installProductsProductRecommendationsDb() :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_products_recommendations"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_products_recommendations (
  id INT(11) NOT NULL AUTO_INCREMENT,
  customers_id int(11) NOT NULL,
  products_id int(11) NOT NULL,
  score float DEFAULT NULL,
  recommendation_date date DEFAULT NULL,
  products_tag varchar(255) DEFAULT NULL,
  customers_group_id int(11) default (0) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE :table_products_recommendations_to_categories (
  id INT(11) NOT NULL AUTO_INCREMENT,
  products_id INT(11) NOT NULL,
  categories_id INT(11) NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_products_id (products_id),
  INDEX idx_categories_id (categories_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
