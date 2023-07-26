<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\ProductRecommendations\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ProductRecommendations extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_ProductRecommendations = Registry::get('ProductRecommendations');

      $this->page->setFile('product_recommendations.php');
      $this->page->data['action'] = 'ProductRecommendations';

      $CLICSHOPPING_ProductRecommendations->loadDefinitions('Sites/ClicShoppingAdmin/ProductRecommendations');
    }
  }