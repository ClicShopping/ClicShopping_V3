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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Manufacturers\Manufacturers as ManufacturersApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Manufacturers')) {
        Registry::set('Manufacturers', new ManufacturersApp());
      }

      $this->app = Registry::get('Manufacturers');
    }

    public function execute()  {
      if (isset($_GET['Insert'])) {
        $Qproducts = $this->app->db->prepare('select products_id
                                              from :table_products
                                              order by products_id desc
                                               limit 1
                                              ');
        $Qproducts->execute();

        $_POST['cPath'] = $this->currentCategoryId;

        $id = $Qproducts->valueInt('products_id');

        $sql_data_array = ['manufacturers_id'  => (int)HTML::sanitize($_POST['manufacturers_id'])];

        $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      }
    }
  }