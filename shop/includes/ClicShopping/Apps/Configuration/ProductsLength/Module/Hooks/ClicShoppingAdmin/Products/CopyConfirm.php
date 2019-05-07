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

  class CopyConfirm implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('ProductsLength')) {
        Registry::set('ProductsLength', new ProductsLengthApp());
      }

      $this->app = Registry::get('ProductsLength');
    }

    public function execute() {
      if (!defined('CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS') || CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS == 'False') {
        return false;
      }

      if (isset($_POST['products_id'])) {
        $current_products_id = HTML::sanitize($_POST['products_id']);

        if (isset($current_products_id) && isset($_GET['CopyConfirm'])) {
          $products_length = $this->app->db->prepare('select products_length_class_id,
                                                              products_dimension_width,
                                                              products_dimension_height,
                                                              products_dimension_depth,
                                                              products_volume
                                                       from :table_products
                                                       where products_id = :products_id
                                                      ');
          $products_length->bindInt(':products_id', $current_products_id);
          $products_length->execute();

          $products_length_class_id = $products_length->valueInt('products_length_class_id');
          $products_dimension_width = $products_length->valueInt('products_dimension_width');
          $products_dimension_height = $products_length->valueInt('products_dimension_height');
          $products_dimension_depth = $products_length->valueInt('products_dimension_depth');
          $products_volume = $products_length->value('products_volume');

          $Qproducts = $this->app->db->prepare('select products_id 
                                                from :table_products                                            
                                                order by products_id desc
                                                limit 1 
                                               ');
          $Qproducts->execute();

          $id = $Qproducts->valueInt('products_id');

          $sql_data_array = ['products_length_class_id'  => (int)$products_length_class_id,
                             'products_dimension_width'  => (float)$products_dimension_width,
                             'products_dimension_height'  => (float)$products_dimension_height,
                             'products_dimension_depth'  => (float)$products_dimension_depth,
                             'products_volume'  => $products_volume
                            ];

          $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
        }
      }
    }
  }