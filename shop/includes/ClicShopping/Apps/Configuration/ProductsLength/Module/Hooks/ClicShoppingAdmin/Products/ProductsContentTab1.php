<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\ProductsLength\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ProductsLength\ProductsLength as ProductsLengthApp;

  use ClicShopping\Apps\Configuration\ProductsLength\Classes\ClicShoppingAdmin\ProductsLengthAdmin;

  class ProductsContentTab1 implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('ProductsLength')) {
        Registry::set('ProductsLength', new ProductsLengthApp());
      }

      $this->app = Registry::get('ProductsLength');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_content_tab_1');
    }

    private function getProductsProductsLength() {
      $Qproducts = $this->app->db->prepare('select products_length_class_id,
                                                   products_dimension_width,
                                                   products_dimension_height,
                                                   products_dimension_depth,
                                                   products_volume
                                            from :table_products
                                            where products_id = :products_id
                                          ');
      $Qproducts->bindInt(':products_id', HTML::sanitize($_GET['pID']));

      $Qproducts->execute();
      $result = $Qproducts->toArray();

      return $result;
    }

    public function display()  {
      if (!defined('CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS') || CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS == 'False') {
        return false;
      }

      $product_length = $this->getProductsProductsLength();

      $products_length_class_id = $product_length['products_length_class_id'];
      $products_dimension_width = $product_length['products_dimension_width'];
      $products_dimension_height = $product_length['products_dimension_height'];
      $products_dimension_depth = $product_length['products_dimension_depth'];
      $products_volume = $product_length['products_volume'];

      if ($products_length_class_id == 0) {
        $products_length_class_id = PRODUCTS_LENGTH_UNIT;
      } else {
        $products_length_class_id = $products_length_class_id;
      }

      $content ='<!----- Products Lenght ---->';
      $content .= '<div class="row col-md-12">';
      $content .= '<div class="col-md-12">';
      $content .= '<div class="row">';

      $content .= '<div class="col-md-5">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_length') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_length') . '</label>';
      $content .= '<div class="col-md-6">';
      $content .= HTML::inputField('products_dimension_width', $products_dimension_width, 'placeholder="' . $this->app->getDef('text_products_dimension_width') . ' "id="products_dimension_width" class="form-control-sm"') . ' <br />' . HTML::inputField('products_dimension_height', $products_dimension_height, 'placeholder="' . $this->app->getDef('text_products_dimension_height') . '" id="products_dimension_height" class="form-control-sm"') . ' <br />' . HTML::inputField('products_dimension_depth', $products_dimension_depth, 'placeholder="' . $this->app->getDef('text_products_dimension_depth') . '" id="products_dimension_depth" class="form-control-sm"');
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $content .= '<div class="col-md-5">';
      $content .= '<div class="form-group row">';
      $content .= '<div class="col-md-5">';
      $content .= $this->app->getDef('text_products_length_type') . '<br /><br />';
      $content .= $this->app->getDef('text_products_volume');
      $content .= '</div>';

      $content .= '<div class="col-md-5">';
      $content .= HTML::selectField('products_length_class_id', ProductsLengthAdmin::getClassesPullDown(), $products_length_class_id);
      $content .= HTML::inputField('products_volume', $products_volume, 'placeholder="' . $this->app->getDef('text_products_volume') . '" id="products_volume" class="form-control-sm"');
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start ProductsLength Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow6').append(
    '{$content}'
);
</script>

<!-- ######################## -->
<!--  End ProductsLength App      -->
<!-- ######################## -->

EOD;
        return $output;

    }
  }