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

  namespace ClicShopping\Apps\Marketing\Featured\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Featured\Featured as FeaturedApp;

  class PageContent implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Featured')) {
        Registry::set('Featured', new FeaturedApp());
      }

      $this->app = Registry::get('Featured');
    }

    public function display()
    {

      if (!defined('CLICSHOPPING_APP_FEATURED_FE_STATUS') || CLICSHOPPING_APP_FEATURED_FE_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/PageContent');

      $content = '<div class="row">';
      $content .= '<div class="col-md-9">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_featured') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_featured') . '</label>';
      $content .= '<div class="col-md-5">';
      $content .= '<ul class="list-group-slider list-group-flush">';
      $content .= '<li class="list-group-item-slider">';
      $content .= '<label class="switch">';
      $content .= HTML::checkboxField('products_featured', 'yes', false, 'class="success"');
      $content .= '<span class="slider"></span>';
      $content .= '</label>';
      $content .= '</li>';
      $content .= '</ul>';
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';


      $output = <<<EOD
<!-- ######################## -->
<!--  Start FeaturedApp      -->
<!-- ######################## -->
<script>
$('#tab9Content').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End FeaturedApp      -->
<!-- ######################## -->

EOD;
      return $output;
    }
  }
