<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\ProductsAttributes\ProductsAttributes as ProductsAttributesApp;

class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('ProductsAttributesApp')) {
      Registry::set('ProductsAttributes', new ProductsAttributesApp());
    }

    $this->app = Registry::get('ProductsAttributes');
  }

  /**
   * @param int $id
   */
  private function removeProducts(int $id): void
  {
    $this->app->db->delete('products_attributes', ['products_id' => (int)$id]);
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_PRODUCTS_ATTRIBUTES_PA_STATUS') || CLICSHOPPING_APP_PRODUCTS_ATTRIBUTES_PA_STATUS == 'False') {
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