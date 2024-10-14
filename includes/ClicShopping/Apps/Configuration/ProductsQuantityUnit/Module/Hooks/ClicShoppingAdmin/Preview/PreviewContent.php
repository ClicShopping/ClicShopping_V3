<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\Hooks\ClicShoppingAdmin\Preview;

use ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit as ProductsQuantityUnitApp;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ProductsQuantityUnit\Classes\ClicShoppingAdmin\ProductsQuantityUnitAdmin;

class PreviewContent implements \ClicShopping\OM\Modules\HooksInterface
{
  private mixed $app;
  protected $qteUnit;

  public function __construct()
  {
    if (!Registry::exists('ProductsQuantityUnit')) {
      Registry::set('ProductsQuantityUnit', new ProductsQuantityUnitApp());
    }

    if (!Registry::exists('ProductsQuantityUnitAdmin')) {
      Registry::set('ProductsQuantityUnitAdmin', new ProductsQuantityUnitAdmin());
    }

    $this->qteUnit = Registry::get('ProductsQuantityUnitAdmin');
    $this->app = Registry::get('ProductsQuantityUnit');
  }


  public function display()
  {
    if (!\defined('CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS') || CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS == 'False') {
      return false;
    }

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Preview/preview_content');

    $content = '<div class="col-md-12">' . $this->app->getDef('text_products_quantity_unit') . ' : ' . $this->qteUnit->getProductsQuantityUnitTitle() . '</div>';

    $output = <<<EOD
<!-- ######################## -->
<!--  Start Product Qty Unit Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow4').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End TwitterApp      -->
<!-- ######################## -->

EOD;
    return $output;

  }
}