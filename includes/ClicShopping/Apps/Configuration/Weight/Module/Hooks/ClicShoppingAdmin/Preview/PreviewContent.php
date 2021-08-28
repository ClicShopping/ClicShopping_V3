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

  namespace ClicShopping\Apps\Configuration\Weight\Module\Hooks\ClicShoppingAdmin\Preview;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\Weight\Classes\ClicShoppingAdmin\WeightAdmin;

  use ClicShopping\Apps\Configuration\Weight\Weight as WeightApp;

  class PreviewContent implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Weight')) {
        Registry::set('Weight', new WeightApp());
      }

      $this->app = Registry::get('Weight');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Preview/preview_content');
    }

    private function getProductsWeight()
    {
      if (isset($_GET['pID'])) {
        $Qproducts = $this->app->db->prepare('select products_weight_class_id,
                                                    products_weight
                                              from :table_products
                                              where products_id = :products_id
                                            ');
        $Qproducts->bindInt(':products_id', HTML::sanitize($_GET['pID']));

        $Qproducts->execute();
        $result = $Qproducts->toArray();

        return $result;
      }
    }


    public function display()
    {
      if (!\defined('CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS') || CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS == 'False') {
        return false;
      }

      $weight = $this->getProductsWeight();
      $products_weight = $weight['products_weight'];
      $products_weight_id = $weight['products_weight_class_id'];

      $content = '<!----- Products Lenght ---->';
      $content .= '<div class="col-md-12">';
      $content .= '<div class="row" id="tab1ContentRow8">';
      $content .= '<div class="col-md-12">';
      $content .= $this->app->getDef('text_products_weight') . ' ' . $products_weight . ' ' . WeightAdmin::getWeightTitle($products_weight_id);
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start Weight Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow3').append(
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