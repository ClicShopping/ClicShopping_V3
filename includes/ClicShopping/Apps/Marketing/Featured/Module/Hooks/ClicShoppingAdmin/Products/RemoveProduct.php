<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Featured\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Featured\Featured as FeaturedApp;

class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Initializes the Featured application by setting it in the Registry if it does not already exist
   * and assigning it to the app property.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Featured')) {
      Registry::set('Featured', new FeaturedApp());
    }

    $this->app = Registry::get('Featured');
  }

  /**
   * Removes products from the featured products list in the database if applicable.
   *
   * @param int $id The ID of the product to be removed.
   * @return void
   */
  private function removeProducts($id)
  {
    if (!empty($_POST['products_featured'])) {
      $this->app->db->delete('products_featured', ['products_id' => (int)$id]);
    }

  }

  /**
   * Executes the main functionality of the method. Checks if the Featured App feature is enabled,
   * then processes the removal of a product if a valid product ID is provided through the POST request.
   *
   * @return bool Returns false if the Featured App feature is disabled, otherwise void.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_FEATURED_FE_STATUS') || CLICSHOPPING_APP_FEATURED_FE_STATUS == 'False') {
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