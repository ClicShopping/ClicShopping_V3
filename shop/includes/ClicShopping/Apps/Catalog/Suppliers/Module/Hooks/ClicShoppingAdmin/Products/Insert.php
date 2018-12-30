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

  namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
    }

    public function execute()  {
      if (isset($_GET['Insert'])) {
        $Qproducts = $this->app->db->prepare('select products_id 
                                              from :table_products                                            
                                              order by products_id desc
                                               limit 1 
                                              ');
        $Qproducts->execute();

        $id = $Qproducts->valueInt('products_id');

        $sql_data_array = ['suppliers_id'  => (int)HTML::sanitize($_POST['suppliers_id'])];

        $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      }
    }
  }