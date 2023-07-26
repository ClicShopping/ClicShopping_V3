<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\ProductRecommendations\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\ProductRecommendations\ProductRecommendations as ProductRecommendationsApp;

  class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('ProductRecommendations')) {
        Registry::set('ProductRecommendations', new ProductRecommendationsApp());
      }

      $this->app = Registry::get('ProductRecommendations');
    }

    private function removeProducts($id)
    {
      if (!empty($_POST['products_recommendations'])) {
        $this->app->db->delete('products_recommendations', ['products_id' => (int)$id]);
        $this->app->db->delete('products_recommendations_to_categories', ['products_id' => (int)$id]);
      }
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_PRODUCT_RECOMMENDATIONS_PR_STATUS') || CLICSHOPPING_APP_PRODUCT_RECOMMENDATIONS_PR_STATUS == 'False') {
        return false;
      }

      if (isset($_POST['remove_id'])) {
        $pID = HTML::sanitize($_POST['remove_id']);
      } elseif (isset($_POST['pID'])) {
        $pID = HTML::sanitize($_POST['pID']);
      } else {
        $pID = false;
      }

      if ($pID !== false) {
        $this->removeProducts($pID);
      }
    }
  }