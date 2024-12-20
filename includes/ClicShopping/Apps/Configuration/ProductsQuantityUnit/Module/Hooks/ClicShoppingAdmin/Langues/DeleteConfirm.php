<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit as ProductsQuantityUnitApp;

class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  public function __construct()
  {
    if (!Registry::exists('ProductsQuantityUnit')) {
      Registry::set('ProductsQuantityUnit', new ProductsQuantityUnitApp());
    }

    $this->app = Registry::get('ProductsQuantityUnit');
  }

  private function delete(int $id)
  {
    if (!\is_null($id)) {
      $this->app->db->delete('products_quantity_unit', ['language_id' => $id]);
    }
  }

  public function execute()
  {
    if (isset($_GET['DeleteConfirm'])) {
      $id = HTML::sanitize($_GET['lID']);
      $this->delete($id);
    }
  }
}