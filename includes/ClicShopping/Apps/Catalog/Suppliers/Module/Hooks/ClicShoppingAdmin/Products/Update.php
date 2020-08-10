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

  class Update implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Update']) && isset($_GET['Products'])) {
        $id = HTML::sanitize($_GET['pID']);

        If (empty($_POST['suppliers_id']) && isset($_GET['Products'])) {
          $suppliers_id = 0;
        } else {
          $suppliers_id = HTML::sanitize($_POST['suppliers_id']);
        }

        $sql_data_array = ['suppliers_id' => $suppliers_id];

        $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      }
    }
  }