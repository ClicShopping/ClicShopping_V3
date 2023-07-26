<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\ProductRecommendations\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\ProductRecommendations\ProductRecommendations;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_ProductRecommendations = new ProductRecommendations();
      Registry::set('ProductRecommendations', $CLICSHOPPING_ProductRecommendations);

      $this->app = $CLICSHOPPING_ProductRecommendations;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
