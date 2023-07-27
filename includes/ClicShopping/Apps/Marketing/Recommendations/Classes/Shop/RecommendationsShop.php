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
    private function getRecommendations(int $products_id) :array
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
     * @param $products_id
     * @return array
     */
    public function displayRecommendations($products_id): array
    {
      $CLICSHOPPING_productsCommon = Registry::get('productsCommon');

      $result_array = $this->getRecommendations($products_id);
      $display_array = [];

      foreach ($result_array as $value) {
        $products_id = $value['products_id'];
        $score = $value['score'];

        $display_array = [
          'products_id' => $products_id,
          'score' => $score,
          'products_name' => $CLICSHOPPING_productsCommon->getProductsName($products_id),
          'products_price' => $CLICSHOPPING_productsCommon->getProductsPrice($products_id),
          'products_description' => $CLICSHOPPING_productsCommon->getProductsDescription($products_id),
          'products_image' => $CLICSHOPPING_productsCommon->getProductsImage($products_id)
        ];
      }

      return $display_array;
    }
  }