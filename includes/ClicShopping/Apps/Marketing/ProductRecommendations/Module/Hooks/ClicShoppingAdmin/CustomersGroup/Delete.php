<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\ProductRecommendations\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\ProductRecommendations\ProductRecommendations as ProductRecommendationsApp;

  class Delete implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('ProductRecommendations')) {
        Registry::set('ProductRecommendations', new ProductRecommendationsApp());
      }

      $this->app = Registry::get('ProductRecommendations');
    }

    /**
     * @param int $group_id
     */
    private function delete(int $group_id) :void
    {
      $QProductsProductRecommendationsCustomersId = $this->app->db->prepare('select count(customers_group_id) as count
                                                                             from :table_products_recommendations
                                                                             where customers_group_id = :customers_group_id
                                                                           ');
      $QProductsProductRecommendationsCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QProductsProductRecommendationsCustomersId->execute();

      if ($QProductsProductRecommendationsCustomersId->valueInt('count') > 0) {
        $Qdelete = $this->app->db->prepare('delete
                                            from :table_products_recommendations
                                            where customers_group_id = :customers_group_id
                                          ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_PRODUCT_RECOMMENDATIONS_PR_STATUS') || CLICSHOPPING_APP_PRODUCT_RECOMMENDATIONS_PR_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Delete'])) {
        $id = HTML::sanitize($_GET['cID']);
        $this->delete($id);
      }
    }
  }