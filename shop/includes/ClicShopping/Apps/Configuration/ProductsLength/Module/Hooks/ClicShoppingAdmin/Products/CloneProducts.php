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

  class CloneProducts implements \ClicShopping\OM\Modules\HooksInterface {
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

      if(isset($_GET['Update']) && isset($_POST['clone_categories_id_to']) && isset($_GET['pID'])) {

        $Qproducts = $this->app->db->prepare('select products_length_class_id,
                                                      products_dimension_width,
                                                      products_dimension_height,
                                                      products_dimension_depth,
                                                      products_volume
                                              from :table_products
                                              where products_id = :products_id
                                             ');
        $Qproducts->bindInt(':products_id', $_GET['pID']);

        $Qproducts->execute();

        $sql_array = ['products_length_class_id' => (int)$Qproducts->valueInt('products_length_class_id'),
                      'products_dimension_width' => (float)$Qproducts->valueInt('products_dimension_width'),
                      'products_dimension_height' => (float)$Qproducts->valueInt('products_dimension_height'),
                      'products_dimension_depth' => (float)$Qproducts->valueInt('products_dimension_depth'),
                      'products_volume' => $Qproducts->value('products_volume')
                     ];

        $insert_array = ['products_id' => HTML::sanitize($_POST['clone_products_id'])];

        $this->app->db->save('products', $sql_array, $insert_array);
      }
    }
  }