<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit as ProductsQuantityUnitApp;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;
  protected mixed $lang;

  public function __construct()
  {
    if (!Registry::exists('ProductsQuantityUnit')) {
      Registry::set('ProductsQuantityUnit', new ProductsQuantityUnitApp());
    }

    $this->app = Registry::get('ProductsQuantityUnit');
    $this->lang = Registry::get('Language');
  }

  private function insert()
  {
    $insert_language_id = LanguageAdmin::getLatestLanguageID();

    $QproductsQuantityUnit = $this->app->db->get('products_quantity_unit', '*', ['language_id' => $this->lang->getId()]);

    while ($QproductsQuantityUnit->fetch()) {
      $cols = $QproductsQuantityUnit->toArray();

      $cols['language_id'] = (int)$insert_language_id;

      $this->app->db->save('products_quantity_unit', $cols);
    }
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS') || CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Langues'], $_GET['Insert'])) {
      $this->insert();
    }
  }
}