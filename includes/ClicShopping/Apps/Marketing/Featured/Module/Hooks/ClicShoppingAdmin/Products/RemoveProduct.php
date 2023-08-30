<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
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
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Featured')) {
      Registry::set('Featured', new FeaturedApp());
    }

    $this->app = Registry::get('Featured');
  }

  private function removeProducts($id)
  {
    if (!empty($_POST['products_featured'])) {
      $this->app->db->delete('products_featured', ['products_id' => (int)$id]);
    }

  }

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