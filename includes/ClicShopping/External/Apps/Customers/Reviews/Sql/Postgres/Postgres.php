<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');
    $CLICSHOPPING_Reviews->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installDbMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Reviews->db->get('administrator_menu', 'app_code', ['app_code' => 'app_customers_reviews']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 6,
        'link' => '',
        'image' => 'reviews.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_customers_reviews',
        'status' => 1
      ];

      $insert_sql_data = ['parent_id' => 4];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Reviews->db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Reviews->db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_Reviews->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Reviews->db->save('administrator_menu_description', $sql_data_array);

// reviews
        $sql_data_array = [
          'sort_order' => 6,
          'link' => 'index.php?A&Customers\\Reviews&Reviews',
          'image' => 'reviews.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_customers_reviews',
          'status' => 1
        ];

        $insert_sql_data = ['parent_id' => 587];
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Reviews->db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Reviews->db->lastInsertId();
        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_array = ['label' => $CLICSHOPPING_Reviews->getDef('title_menu')];

          $insert_sql_data = [
            'id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Reviews->db->save('administrator_menu_description', $sql_data_array);
        }

//sentiment
        $sql_data_array = [
          'sort_order' => 7,
          'link' => 'index.php?A&Customers\\Reviews&ReviewsSentiment',
          'image' => 'reviews.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_customers_reviews',
          'status' => 1
        ];

        $insert_sql_data = ['parent_id' => 587];
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Reviews->db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Reviews->db->lastInsertId();
        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_array = ['label' => $CLICSHOPPING_Reviews->getDef('title_menu_sentiment')];

          $insert_sql_data = [
            'id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Reviews->db->save('administrator_menu_description', $sql_data_array);
        }

//Statistics vote
        $sql_data_array = [
          'sort_order' => 4,
          'link' => 'index.php?A&Customers\\Reviews&StatsCustomersVote',
          'image' => 'reviews.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_customers_reviews',
          'status' => 1
        ];

        $insert_sql_data = ['parent_id' => 98];
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Reviews->db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Reviews->db->lastInsertId();
        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_array = ['label' => $CLICSHOPPING_Reviews->getDef('title_menu_statistics_vote')];

          $insert_sql_data = [
            'id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Reviews->db->save('administrator_menu_description', $sql_data_array);
        }
      }

      Cache::clear('menu-administrator');
    }
  }

  /**
   * @return void
   */
  private static function installDb()
  {
    $CLICSHOPPING_Reviews = Registry::get('Reviews');

    $Qcheck = $CLICSHOPPING_Reviews->db->query("SELECT to_regclass(':table_reviews')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_reviews (
      reviews_id serial PRIMARY KEY,
      products_id int NOT NULL,
      customers_id int,
      customers_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      reviews_rating int,
      date_added timestamp,
      last_modified timestamp,
      reviews_read int DEFAULT 0 NOT NULL,
      status smallint DEFAULT 0 NOT NULL,
      customers_group_id int DEFAULT 0 NOT NULL,
      customers_tag varchar(255),
      CONSTRAINT fk_reviews_products
        FOREIGN KEY (products_id)
        REFERENCES :table_products (products_id),
      CONSTRAINT fk_reviews_customers
        FOREIGN KEY (customers_id)
        REFERENCES :table_customers (customers_id),
      CONSTRAINT fk_reviews_customers_group
        FOREIGN KEY (customers_group_id)
        REFERENCES :table_customers_groups (customers_group_id)
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Reviews->db->exec($sql);

      $Qcheck = $CLICSHOPPING_Reviews->db->query("SELECT to_regclass(':table_reviews_description')");

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
    CREATE TABLE :table_reviews_description (
      reviews_id int NOT NULL,
      languages_id int NOT NULL,
      reviews_text text COLLATE utf8mb4_unicode_ci NOT NULL,
      PRIMARY KEY (reviews_id, languages_id),
      CONSTRAINT fk_reviews_description_reviews
        FOREIGN KEY (reviews_id)
        REFERENCES :table_reviews (reviews_id)
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $CLICSHOPPING_Reviews->db->exec($sql);
      }
    }
  }
}
