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

  namespace ClicShopping\Apps\Marketing\Favorites\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

  class PageContent implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Favorites')) {
        Registry::set('Favorites', new FavoritesApp());
      }

      $this->app = Registry::get('Favorites');
    }

    public function display()
    {

      if (!defined('CLICSHOPPING_APP_FAVORITES_FA_STATUS') || CLICSHOPPING_APP_FAVORITES_FA_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/PageContent');

      $content = '<div class="row">';
      $content .= '<div class="col-md-9">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_favorites') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_favorites') . '</label>';
      $content .= '<div class="col-md-5">';
      $content .= HTML::checkboxField('products_favorites', 'yes', false);
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start FavoritesApp      -->
<!-- ######################## -->
<script>
$('#tab9Content').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End FavoritesApp      -->
<!-- ######################## -->

EOD;
      return $output;
    }
  }
