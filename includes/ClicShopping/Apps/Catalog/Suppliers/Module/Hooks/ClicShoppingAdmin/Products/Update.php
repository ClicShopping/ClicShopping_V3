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

  namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;
  use ClicShopping\Apps\Catalog\Suppliers\Classes\ClicShoppingAdmin\SupplierAdmin;

  class Update implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;
    protected $supplierAdmin;
    
    public function __construct()
    {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
  
      if (!Registry::exists('SupplierAdmin')) {
        Registry::set('SupplierAdmin', new SupplierAdmin());
      }
  
      $this->supplierAdmin = Registry::get('SupplierAdmin');
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Update'], $_GET['Products'])) {
        $id = HTML::sanitize($_GET['pID']);

        $suppliers_id = $this->supplierAdmin->getSupplierId($_POST['suppliers_name']);

        $sql_data_array = ['suppliers_id' => (int)$suppliers_id];

        $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      }
    }
  }