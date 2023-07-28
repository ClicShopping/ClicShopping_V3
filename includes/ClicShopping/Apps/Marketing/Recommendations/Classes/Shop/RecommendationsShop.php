<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Marketing\Recommendations\Classes\Shop;

  use ClicShopping\Apps\Marketing\Recommendations\Classes\ClicShoppingAdmin\RecommendationsAdmin;

  use ClicShopping\OM\Registry;

  class RecommendationsShop
  {
    /**
     * Save the recommendation about a specific product and the customer
     * @param int $products_id
     * @param float $reviewRate
     */
    public static function saveRecommendations(int $products_id, float $reviewRate = 0): void
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      Registry::set('RecommendationsAdmin', new RecommendationsAdmin());
      $CLICSHOPPING_RecommendationsAdmin = Registry::get('RecommendationsAdmin');

      $products_rate_weight = $CLICSHOPPING_RecommendationsAdmin->calculateProductsRateWeight($products_id);

      $customer_id = $CLICSHOPPING_Customer->getID();
      $customer_group_id = $CLICSHOPPING_Customer->getCustomersGroupID();

      //to improve with strategy
      $score = $CLICSHOPPING_RecommendationsAdmin->calculateRecommendationScore($products_rate_weight, $reviewRate, null, CLICSHOPPING_APP_RECOMMENDATIONS_PR_STRATEGY);

      if ($score != 0) {
        $sql_data_array = [
          'score' => $score,
          'recommendation_date' => 'now()',
          'customers_group_id' => $customer_group_id
        ];

        $insert_sql_data = [
          'products_id' => $products_id,
          'customers_id' => $customer_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('products_recommendations', $sql_data_array);

        $category_id = self::getProductCategoryID($products_id);

        $insert_sql_data = [
          'products_id' => $products_id,
          'categories_id' => $category_id
        ];

        $CLICSHOPPING_Db->save('products_recommendations_to_categories', $insert_sql_data);
      }
    }

    /**
     * Function to generate product recommendations for a specific customer
     * @return array
     */
    private function generateCustomerRecommendations($products_id): array
    {
      $currentProductCategory = self::getProductCategoryID($products_id);

      $Qrecommendations = $this->db->prepare('SELECT pr.products_id,
                                                     pr.score,
                                                     pr.recommendation_date
                                              FROM :table_products_recommendations pr
                                              INNER JOIN :table_products_to_categories pc
                                              ON pr.products_id = pc.products_id
                                              WHERE pr.customers_id = :customers_id
                                              AND pc.categories_id = :category_id
                                              ORDER BY pr.score DESC
                                             ');

      $Qrecommendations->bindInt(':customers_id', $this->customer->getID());
      $Qrecommendations->bindInt(':category_id', $currentProductCategory);
      $Qrecommendations->execute();

      $recommendations = $Qrecommendations->fetchAll();

      return $recommendations;
    }

    /**
     * Function to generate product recommendations listing
     * @param $products_id
     * @return array
     */
    private function getRecommendations(int $products_id): array
    {
      $recommendation_array = self::generateCustomerRecommendations($products_id);

      $result = [];

      foreach ($recommendation_array as $recommendation) {
        $Qproducts = $this->db->prepare('select products_id
                                                 products_price p,
                                                 products_name pd,
                                                 products_description pd
                                          from :table_products p,
                                               :table_products_description pd
                                          where p.products_id = :products_id
                                          and  p.products_id = pd.products_id
                                          and  pd.language_id = :language_id
                                         ');
        $Qproducts->bindInt(':products_id', $products_id);
        $Qproducts->bindInt(':language_id', $this->language->getId());
        $Qproducts->execute();

        $recommendations = $Qproducts->fetch();

        $result = [
          'products_id' => $recommendation['products_id'],
          'score' => $recommendation['score']
        ];

        $result = array_merge($recommendations, $result);
      }

      return $result;
    }

    /**
     * Get the category ID of the current product
     * @return int
     */
    private static function getProductCategoryID(int $products_id): int
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $QproductCategory = $CLICSHOPPING_Db->prepare('SELECT categories_id 
                                                      FROM :table_products_to_categories
                                                      WHERE products_id = :products_id'
      );
      $QproductCategory->bindInt(':products_id', $products_id);
      $QproductCategory->execute();

      return $QproductCategory->valueInt('categories_id');
    }


    /**
     * @return array
     */


    public static function getCountColumnList()
    {
// create column list
      $define_list = [
        'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_DATE_ADDED' => MODULE_PRODUCTS_RECOMMENDATIONS_LIST_DATE_ADDED,
        'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_PRICE' => MODULE_PRODUCTS_RECOMMENDATIONS_LIST_PRICE,
        'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_MODEL' => MODULE_PRODUCTS_RECOMMENDATIONS_LIST_MODEL,
        'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_WEIGHT' => MODULE_PRODUCTS_RECOMMENDATIONS_LIST_WEIGHT,
        'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_QUANTITY' => MODULE_PRODUCTS_RECOMMENDATIONS_LIST_QUANTITY,
      ];

      asort($define_list);

      $column_list = [];

      foreach ($define_list as $key => $value) {
        if ($value > 0) $column_list[] = $key;
      }

      return $column_list;
    }

    private static function Listing()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qlisting = 'select distinct SQL_CALC_FOUND_ROWS ';

      $count_column = static::getCountColumnList();

      for ($i = 0, $n = \count($count_column); $i < $n; $i++) {
        switch ($count_column[$i]) {
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_DATE_ADDED':
            $Qlisting .= ' p.products_date_added, ';
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_PRICE':
            $Qlisting .= ' p.products_price, ';
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_MODEL':
            $Qlisting .= ' p.products_model, ';
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_WEIGHT':
            $Qlisting .= ' p.products_weight, ';
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_QUANTITY':
            $Qlisting .= ' p.products_quantity, ';
            break;
        }
      }

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $Qlisting .= ' p.products_id,
                       p.products_quantity,
		                   pr.score
                  from :table_products_recommendations pr join :table_products_groups g on pr.products_id = g.products_id,
                    :table_products p,
                    :table_products_to_categories p2c,
                    :table_categories c                                              
                  where pr.score > 0.5
                  and p.products_status = 1
                    and g.price_group_view = 1                 
                    and p.products_id = pr.products_id
                    and g.customers_group_id = :customers_group_id
                    and g.products_group_view = 1
                    and p.products_archive = 0
                    and pr.products_id = p.products_id
                    and (pr.customers_group_id = :customers_group_id or pr.customers_group_id = 99)
                    and p.products_id = p2c.products_id
                    and p2c.categories_id = c.categories_id
                    and c.virtual_categories = 0
                    and c.status = 1
                   ';
      } else {
        $Qlisting .= '   p.products_id,                      
                         p.products_quantity,
		                     pr.score
                    from :table_products_recommendations pr,
                         :table_products p,
                         :table_products_to_categories p2c,
                         :table_categories c                                     
                    where pr.score > 0.5
                    and p.products_id = pr.products_id
                    and p.products_status = 1
                    and p.products_view = 1
                    and p.products_archive = 0
                    and (pr.customers_group_id = 0 or pr.customers_group_id = 99)
                    and p.products_id = p2c.products_id
                    and p2c.categories_id = c.categories_id
                    and c.virtual_categories = 0
                    and c.status = 1
                   ';
      }

      if ((!isset($_GET['sort'])) || (!preg_match('/^[1-8][ad]$/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > \count($count_column))) {
        for ($i = 0, $n = \count($count_column); $i < $n; $i++) {
          if ($count_column[$i] == 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_DATE_ADDED') {
            $_GET['sort'] = $i + 1 . 'a';
            $Qlisting .= ' order by pr.score DESC ';
            break;
          }
        }
      } else {

        $sort_col = substr($_GET['sort'], 0, 1);
        $sort_order = substr($_GET['sort'], 1);

        switch ($count_column[$sort_col - 1]) {
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_DATE_ADDED':
            $Qlisting .= ' order by p.products_date_added ' . ($sort_order == 'd' ? 'desc' : ' ');
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_PRICE':
            $Qlisting .= ' order by p.products_price ' . ($sort_order == 'd' ? 'desc' : '') . ', p.products_date_added DESC ';
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_MODEL':
            $Qlisting .= ' order by p.products_model ' . ($sort_order == 'd' ? 'desc' : '') . ', pr.products_date_added DESC ';
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_QUANTITY':
            $Qlisting .= ' order by p.products_quantity ' . ($sort_order == 'd' ? 'desc' : '') . ', pr.products_date_added DESC ';
            break;
          case 'MODULE_PRODUCTS_RECOMMENDATIONS_LIST_WEIGHT':
            $Qlisting .= ' order by p.products_weight ' . ($sort_order == 'd' ? 'desc' : '') . ', pr.products_date_added DESC ';
            break;
        }
      }

      $Qlisting .= ' limit :page_set_offset,
                       :page_set_max_results
                   ';

      return $Qlisting;
    }

    public static function getListing()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qlisting = static::Listing();

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $QlistingRecommendations= $CLICSHOPPING_Db->prepare($Qlisting);
        $QlistingRecommendations->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
      } else {
        $QlistingRecommendations = $CLICSHOPPING_Db->prepare($Qlisting);
      }

      return $QlistingRecommendations;
    }
  }