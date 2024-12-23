<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Specials\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Specials\Specials as SpecialsApp;

class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Constructs the class and ensures that the 'Specials' application is registered in the registry.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Specials')) {
      Registry::set('Specials', new SpecialsApp());
    }

    $this->app = Registry::get('Specials');
  }

  /**
   * Removes products from the specials table in the database based on the given product ID.
   *
   * @param int $id The ID of the product to be removed from the specials.
   * @return void
   */
  private function removeProducts($id)
  {
    if (!empty($_POST['products_specials'])) {
      $this->app->db->delete('specials', ['products_id' => (int)$id]);
    }
  }

  /**
   * Executes the removal of products based on the provided product identifier.
   *
   * @return void
   */
  public function execute()
  {
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