<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Recommendations\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ProductsRecommendation extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Recommendations = Registry::get('Recommendations');

      $this->page->setFile('products_recommendation.php');
      $this->page->data['action'] = 'Recommendations';

      $CLICSHOPPING_Recommendations->loadDefinitions('Sites/ClicShoppingAdmin/Recommendations');
    }
  }