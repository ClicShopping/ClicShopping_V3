<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Recommendations\Recommendations as RecommendationsApp;
use function defined;

class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Constructor method for initializing the Recommendations app.
   *
   * Checks if the 'Recommendations' application is registered in the Registry.
   * If not, it creates and registers a new RecommendationsApp instance.
   * Then, it retrieves and assigns the 'Recommendations' app instance to the class property.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Recommendations')) {
      Registry::set('Recommendations', new RecommendationsApp());
    }

    $this->app = Registry::get('Recommendations');
  }

  /**
   * Removes product recommendations and associated category mappings for a given product ID.
   *
   * @param int $id The ID of the product to remove recommendations for.
   * @return void
   */
  private function removeProducts($id)
  {
    if (!empty($_POST['products_recommendations'])) {
      $this->app->db->delete('products_recommendations', ['products_id' => (int)$id]);
      $this->app->db->delete('products_recommendations_to_categories', ['products_id' => (int)$id]);
    }
  }

  /**
   * Executes the main functionality of the method. Checks if the recommendations module is active
   * and processes a product removal request if a product ID is provided.
   *
   * @return bool Returns false if the recommendations module is inactive, otherwise void.
   */
  public function execute()
  {
    if (!defined('CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS') || CLICSHOPPING_APP_RECOMMENDATIONS_PR_STATUS == 'False') {
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