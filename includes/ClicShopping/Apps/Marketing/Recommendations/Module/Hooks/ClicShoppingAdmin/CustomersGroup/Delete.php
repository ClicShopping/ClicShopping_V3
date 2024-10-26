<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Recommendations\Recommendations as RecommendationsApp;
use function defined;

class Delete implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Recommendations')) {
      Registry::set('Recommendations', new RecommendationsApp());
    }

    $this->app = Registry::get('Recommendations');
  }

  /**
   * @param int $group_id
   */
  private function delete(int $group_id): void
  {
    $QProductsRecommendationsCustomersId = $this->app->db->prepare('select count(customers_group_id) as count
                                                                             from :table_products_recommendations
                                                                             where customers_group_id = :customers_group_id
                                                                           ');
    $QProductsRecommendationsCustomersId->bindInt(':customers_group_id', (int)$group_id);
    $QProductsRecommendationsCustomersId->execute();

    if ($QProductsRecommendationsCustomersId->valueInt('count') > 0) {
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
    if (!defined('CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS') || CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Delete'])) {
      $id = HTML::sanitize($_GET['cID']);
      $this->delete($id);
    }
  }
}