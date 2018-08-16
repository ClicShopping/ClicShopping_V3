<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsAttributes;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;


  class AddProductOptions extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('ProductsAttributes');
    }

    public function execute() {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');
      $products_options_id = HTML::sanitize($_POST['products_options_id']);
      $option_name_array = $_POST['option_name'];
      $option_sort_order = $_POST['option_sort_order'];

      $option_page = (isset($_GET['option_page']) && is_numeric($_GET['option_page'])) ? $_GET['option_page'] : 1;
      $value_page = (isset($_GET['value_page']) && is_numeric($_GET['value_page'])) ? $_GET['value_page'] : 1;
      $attribute_page = (isset($_GET['attribute_page']) && is_numeric($_GET['attribute_page'])) ? $_GET['attribute_page'] : 1;
      $page_info = 'option_page=' . HTML::sanitize($option_page) . '&value_page=' . HTML::sanitize($value_page) . '&attribute_page=' . HTML::sanitize($attribute_page);

      $languages = $CLICSHOPPING_Language->getLanguages();

      $products_options_id = HTML::sanitize($_POST['products_options_id']);
      $option_name_array = HTML::sanitize($_POST['option_name']);
      $option_sort_order = HTML::sanitize($_POST['option_sort_order']);

      for ($i=0, $n=count($languages); $i<$n; $i ++) {
        $option_name = HTML::sanitize($option_name_array[$languages[$i]['id']]);

        $this->app->db->save('products_options', [
                                              'products_options_id' =>  (int)$products_options_id,
                                              'products_options_name' => $option_name,
                                              'language_id' => (int)$languages[$i]['id'],
                                              'products_options_sort_order' => (int)$option_sort_order
                                              ]
                        );

      }

      $CLICSHOPPING_Hooks->call('AddProductOptions','AddProductOption');

      $this->app->redirect('ProductsAttributes', $page_info);
    }
  }