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

  namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit as ProductsQuantityUnitApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('ProductsQuantityUnit')) {
        Registry::set('ProductsQuantityUnit', new ProductsQuantityUnitApp());
      }

      $this->app = Registry::get('ProductsQuantityUnit');
    }

    public function execute()
    {
      if (isset($_GET['Insert'], $_POST['products_quantity'], $_POST['products_quantity_unit_id'])) {
        $Qproducts = $this->app->db->prepare('select products_id 
                                              from :table_products                                            
                                              order by products_id desc
                                              limit 1 
                                            ');
        $Qproducts->execute();

        $id = $Qproducts->valueInt('products_id');

        $sql_data_array = ['products_quantity' => (int)HTML::sanitize($_POST['products_quantity']),
          'products_quantity_unit_id' => (int)HTML::sanitize($_POST['products_quantity_unit_id']),
        ];

        $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      }
    }
  }