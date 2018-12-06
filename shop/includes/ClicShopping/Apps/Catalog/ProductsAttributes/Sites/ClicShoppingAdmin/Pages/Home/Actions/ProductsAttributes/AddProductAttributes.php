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
  use ClicShopping\OM\Upload;

  class AddProductAttributes extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('ProductsAttributes');
    }

    public function execute() {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $products_id = HTML::sanitize($_POST['products_id']);
      $options_id = HTML::sanitize($_POST['options_id']);
      $values_id = HTML::sanitize($_POST['values_id']);
      $value_price = HTML::sanitize($_POST['value_price']);
      $price_prefix = HTML::sanitize($_POST['price_prefix']);
      $value_sort_order = HTML::sanitize($_POST['value_sort_order']);
      $products_attributes_reference = HTML::sanitize($_POST['products_attributes_reference']);
      $customers_group_id = HTML::sanitize($_POST['customers_group_id']);

      $option_page = (isset($_GET['option_page']) && is_numeric($_GET['option_page'])) ? $_GET['option_page'] : 1;
      $value_page = (isset($_GET['value_page']) && is_numeric($_GET['value_page'])) ? $_GET['value_page'] : 1;
      $attribute_page = (isset($_GET['attribute_page']) && is_numeric($_GET['attribute_page'])) ? $_GET['attribute_page'] : 1;

      $page_info = 'option_page=' . HTML::sanitize($option_page) . '&value_page=' . HTML::sanitize($value_page) . '&attribute_page=' . HTML::sanitize($attribute_page);

      $this->app->db->save('products_attributes', ['products_id' => (int)$products_id,
                                                  'options_id' => (int)$options_id,
                                                  'options_values_id' => (int)$values_id,
                                                  'options_values_price' => (float)$value_price,
                                                  'price_prefix' => $price_prefix,
                                                  'products_options_sort_order' => (int)$value_sort_order,
                                                  'products_attributes_reference' => $products_attributes_reference,
                                                  'customers_group_id' => $customers_group_id
                                                  ]
                          );

      if (DOWNLOAD_ENABLED == 'true') {

        $products_attributes_id = $this->app->db->lastInsertId();

        $products_attributes_maxdays = HTML::sanitize($_POST['products_attributes_maxdays']);
        $products_attributes_maxcount = HTML::sanitize($_POST['products_attributes_maxcount']);

        $upload_file = new Upload('new_products_attributes_filename', $CLICSHOPPING_Template->getPathDownloadShopDirectory('Private'));

        $error = true;

        if ( $upload_file->check() && $upload_file->save()) {
          $error = false;
        }

        if ( $error === false ) {
          $this->app->db->save('products_attributes_download', ['products_attributes_id' => (int)$products_attributes_id,
                                                                'products_attributes_filename' => $upload_file->getFilename(),
                                                                'products_attributes_maxdays' => (int)$products_attributes_maxdays,
                                                                'products_attributes_maxcount' => (int)$products_attributes_maxcount
                                                                ]
                               );

        } else {
           $this->app->db->save('products_attributes_download', ['products_attributes_id' => (int)$products_attributes_id,
                                                                 'products_attributes_filename' => '',
                                                                 'products_attributes_maxdays' => (int)$products_attributes_maxdays,
                                                                 'products_attributes_maxcount' => (int)$products_attributes_maxcount
                                                                ]
                              );
        }
      }

      $CLICSHOPPING_Hooks->call('AddProductAttribute','Insert');

      $this->app->redirect('ProductsAttributes', $page_info);
    }
  }