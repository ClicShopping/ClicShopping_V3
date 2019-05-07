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
      if (isset($_GET['Update']) && isset($_GET['pID']) && isset($_POST['products_quantity_unit_id'])) {
        $id = HTML::sanitize($_GET['pID']);

        $Qupdate = $this->app->db->prepare('update :table_products
                                            set products_quantity_unit_id = :products_quantity_unit_id
                                            where products_id = :products_id
                                          ');
        $Qupdate->bindInt(':products_quantity_unit_id', $_POST['products_quantity_unit_id'] );
        $Qupdate->bindInt(':products_id', $id);
        $Qupdate->execute();
      }
    }
  }