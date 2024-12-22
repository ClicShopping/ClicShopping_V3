<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Favorites\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Constructor method to initialize the Favorites application.
   *
   * Checks if the 'Favorites' instance exists in the Registry. If not, it creates
   * a new instance of the FavoritesApp and adds it to the Registry. Then, it
   * retrieves the instance from the Registry and assigns it to the app property.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Favorites')) {
      Registry::set('Favorites', new FavoritesApp());
    }

    $this->app = Registry::get('Favorites');
  }

  /**
   * Removes a product from the favorites list based on the provided ID.
   *
   * @param int $id The ID of the product to be removed from the favorites list.
   * @return void
   */
  private function removeProducts($id)
  {
    if (!empty($_POST['products_favorites'])) {
      $this->app->db->delete('products_favorites', ['products_id' => (int)$id]);
    }
  }

  /**
   * Executes the functionality for removing products based on the provided input parameters.
   *
   * @return bool Returns false if the application status is not defined or set to 'False', otherwise performs the operation and does not return any value.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_FAVORITES_FA_STATUS') || CLICSHOPPING_APP_FAVORITES_FA_STATUS == 'False') {
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