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

  namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit as ProductsQuantityUnitApp;

  class Update implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('ProductsQuantityUnit')) {
        Registry::set('ProductsQuantityUnit', new ProductsQuantityUnitApp());
      }

      $this->app = Registry::get('ProductsQuantityUnit');
    }

    public function execute()  {
      if (isset($_GET['Update'])) {
        $id = HTML::sanitize($_GET['pID']);

        $sql_data_array = ['products_quantity_unit_id'  => (int)HTML::sanitize($_POST['products_quantity_unit_id'])];

        $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      }
    }
  }