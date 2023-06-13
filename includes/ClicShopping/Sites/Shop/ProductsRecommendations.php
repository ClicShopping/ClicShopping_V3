<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Registry;

  class ProductsRecommendations
  {
    protected mixed $db;
    private mixed $productsFunctionTemplate;
    private mixed $productsCommon;
    private mixed $customer;
    private $products_id;

    public function __construct()
    {
      $this->db = Registry::get('Db');
      $this->productsFunctionTemplate = Registry::get('ProductsFunctionTemplate');
      $this->productsCommon = Registry::get('ProductsCommon');

      $this->customer = Registry::get('Customer');
      $this->products_id = $this->productsCommon->getID();
    }

    /**
     * Function to generate product recommendations for a specific customer
     * @return array
     */
    public function generateCustomerRecommendations() :array
    {
      $Qrecommendations = $this->db->prepare('select products_id,
                                                     score
                                             from :table_products_recommendations
                                             where customers_id = :customers_id
                                             order by score desc
                                            ');
      $Qrecommendations->bindInt(':customers_id', $this->customer->getID());
      $Qrecommendations->execute();

      $recommendations = $Qrecommendations->fetchAll();

      return $recommendations;
    }

    /**
     * @return float
     * Function to calculate the score for product recommendations
     */
    public function calculateRecommendationScore(float $productsRateWeight = 0.8) :float
    {
      $CLICSHOPPING_Reviews = Registry::get('Reviews');
      $avg_review = $CLICSHOPPING_Reviews->getAverageProductReviews($this->products_id, true);

      $score = $avg_review * $productsRateWeight;

      return $score;
    }

    /**
     * Save the recommendation about a specific product and the customer
     * @param int|null $products_id
     */
    public function saveProductRecommendations(?int $products_id) :void
    {
      if (\is_null($products_id)) {
        $products_id = $this->products_id;
      }

      $customer_id = $this->customer->getID();

      $score = $this->calculateRecommendationScore();

      if ($score != 0) {
        $sql_data_array = [
          'score' => $score,
          'recommendation_date' => 'now()'
        ];

        $insert_sql_data = [
          'products_id' => $products_id,
          'customers_id' => $customer_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->db->save('products_recommendations', $sql_data_array);
      }
    }


    /**
     * Function to generate product recommendations listing
     * @param $products_id
     * @return array
     */
    public function getProductRecommendations($products_id) :array
    {
      $recommendation_array = $this->productsCommon->generateCustomerRecommendations();
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
     * @param $products_id
     * @return array
     */
    public function displayRecommendations($products_id): array
    {
      $result_array = $this->getProductRecommendations($products_id);
      $display_array = [];

      foreach ($result_array as $value) {
        $products_id = $value['products_id'];
        $score = $value['score'];

        $display_array = [
          'products_id' => $products_id,
          'score' => $score,
          'products_name' => $this->productsFunctionTemplate->getProductsName($products_id),
          'products_url' => $this->productsFunctionTemplate->getProductsNameUrl($products_id),
          'products_price' => '',
          'products_description' => $this->productsCommon->getProductsDescription($products_id),
          'products_image' => $this->productsFunctionTemplate->getImage('Small', $products_id, '', true, '')
        ];
      }

      return $display_array;
    }
  }

