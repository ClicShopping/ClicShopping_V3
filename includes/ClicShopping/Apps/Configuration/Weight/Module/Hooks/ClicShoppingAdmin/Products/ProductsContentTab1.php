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

  namespace ClicShopping\Apps\Configuration\Weight\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\Weight\Weight as WeightApp;

  use ClicShopping\Apps\Configuration\Weight\Classes\ClicShoppingAdmin\WeightAdmin;

  class ProductsContentTab1 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Weight')) {
        Registry::set('Weight', new WeightApp());
      }

      $this->app = Registry::get('Weight');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_content_tab_1');
    }

    private function getProductsWeightId()
    {
      $Qproducts = $this->app->db->prepare('select products_weight_class_id
                                            from :table_products
                                            where products_id = :products_id
                                          ');
      $Qproducts->bindInt(':products_id', HTML::sanitize($_GET['pID']));

      $Qproducts->execute();

      return $Qproducts->valueInt('products_weight_class_id');
    }

    public function display()
    {
      if (!\defined('CLICSHOPPING_APP_WEIGHT_WE_STATUS') || CLICSHOPPING_APP_WEIGHT_WE_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['pID'])) {
        $products_weight_class_id = $this->getProductsWeightId();
      } else {
        $products_weight_class_id = 0;
      }

      if ($products_weight_class_id == 0) {
        $weight_class_id = SHIPPING_WEIGHT_UNIT;
      } else {
        $weight_class_id = $products_weight_class_id;
      }

      $content = '<div class="col-md-5">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_weight_type') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_weight_type') . '</label>';
      $content .= '<div class="col-md-5">';
      $content .= HTML::selectField('products_weight_class_id', WeightAdmin::getClassesPullDown(), $weight_class_id) . '<br />';
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start Weight Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow5').append(
    '{$content}'
);
</script>

<!-- ######################## -->
<!--  End Weight App      -->
<!-- ######################## -->

EOD;
      return $output;

    }
  }