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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class DeleteOption extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('ProductsAttributes');
    }

    public function execute() {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $option_page = (isset($_GET['option_page']) && is_numeric($_GET['option_page'])) ? $_GET['option_page'] : 1;
      $value_page = (isset($_GET['value_page']) && is_numeric($_GET['value_page'])) ? $_GET['value_page'] : 1;
      $attribute_page = (isset($_GET['attribute_page']) && is_numeric($_GET['attribute_page'])) ? $_GET['attribute_page'] : 1;

      $page_info = 'option_page=' . HTML::sanitize($option_page) . '&value_page=' . HTML::sanitize($value_page) . '&attribute_page=' . HTML::sanitize($attribute_page);

//      $option_id = HTML::sanitize($_POST['option_id']);
      $option_id = HTML::sanitize($_GET['option_id']);

      $Qdelete = $this->app->db->prepare('delete
                                          from :table_products_options
                                          where products_options_id = :products_options_id
                                        ');
      $Qdelete->bindInt(':products_options_id', $option_id);
      $Qdelete->execute();

      $CLICSHOPPING_Hooks->call('DeleteOption','Delete');

      $this->app->redirect('ProductsAttributes&' . $page_info);
    }
  }