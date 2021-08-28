<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ProductsQuantityUnit\Classes\ClicShoppingAdmin\ProductsQuantityUnitAdmin;

  use ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit as ProductsQuantityUnitApp;

  class ProductsContentTab2 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;
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

    private function getQtyUnit()
    {
      if (isset($_GET['pID'])) {
        $QtyUnit = $this->app->db->prepare('select products_quantity_unit_id
                                             from :table_products
                                             where  products_id = :products_id
                                           ');
        $QtyUnit->bindInt(':products_id', HTML::sanitize($_GET['pID']));

        $QtyUnit->execute();

        return $QtyUnit->valueInt('products_quantity_unit_id');
      }
    }


    public function display()
    {
      if (!\defined('CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS') || CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_content_tab_2');

      $products_quantity_unit_drop_down = $this->qteUnit->productsQuantityUnitDropDown();

      $content = '<div class="col-md-5">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_quantity_unit') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_quantity_unit') . '</label>';
      $content .= '<div class="col-md-5">';
      $content .= HTML::selectMenu('products_quantity_unit_id', $products_quantity_unit_drop_down, $this->getQtyUnit());
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';


      $output = <<<EOD
<!-- ######################## -->
<!--  Start Product Qty Unit Hooks      -->
<!-- ######################## -->
<script>
$('#tab2ContentRow5').prepend(
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