<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Recommendations\Module\Hooks\Shop\Reviews;

  use ClicShopping\Apps\Marketing\Recommendations\Classes\Shop\RecommendationsShop;
  use ClicShopping\OM\Registry;

  class saveEntry implements \ClicShopping\OM\Modules\HooksInterface
  {
    public function __construct()
    {
      $this->productsCommon = Registry::get('ProductsCommon');
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS') || CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS == 'False') {
        return false;
      }

      RecommendationsShop::saveRecommendations($this->productsCommon->getID(), (int)$_POST['rating']);
    }
  }