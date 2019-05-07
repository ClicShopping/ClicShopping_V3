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

  class Update implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('ProductsLength')) {
        Registry::set('ProductsLength', new ProductsLengthApp());
      }

      $this->app = Registry::get('ProductsLength');
    }

    public function execute()  {
      if (!defined('CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS') || CLICSHOPPING_APP_PROUCTS_LENGTH_PL_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Update']) && isset($_GET['pID'])) {
        $id = HTML::sanitize($_GET['pID']);

        $sql_data_array = ['products_length_class_id' => (int)HTML::sanitize($_POST['products_length_class_id']),
                          'products_dimension_width' => (float)HTML::sanitize($_POST['products_dimension_width']),
                          'products_dimension_height' => (float)HTML::sanitize($_POST['products_dimension_height']),
                          'products_dimension_depth' => (float)HTML::sanitize($_POST['products_dimension_height']),
                          'products_volume' => HTML::sanitize($_POST['products_volume'])
                          ];

        $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      }
    }
  }