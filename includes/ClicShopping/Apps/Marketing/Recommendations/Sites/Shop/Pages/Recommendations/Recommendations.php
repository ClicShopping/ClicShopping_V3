<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Recommendations\Sites\Shop\Pages\Recommendations;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Recommendations\Recommendations as RecommendationsApp;

  class Recommendations extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      if (!Registry::exists('Recommendations')) {
        Registry::set('Recommendations', new RecommendationsApp());
      }

      $CLICSHOPPING_ProductsRecommendation = Registry::get('Recommendations');

      $CLICSHOPPING_ProductsRecommendation->loadDefinitions('Sites/Shop/main');
    }
  }
