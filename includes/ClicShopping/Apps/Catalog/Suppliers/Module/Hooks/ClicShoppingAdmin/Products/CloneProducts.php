<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

class CloneProducts implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Initializes the Suppliers application by checking if it exists in the registry.
   * If not, it creates a new instance and stores it in the registry.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Suppliers')) {
      Registry::set('Suppliers', new SuppliersApp());
    }

    $this->app = Registry::get('Suppliers');
  }

  /**
   * Executes the logic for handling the suppliers' application state and cloning product information.
   *
   * @return bool Returns false if the suppliers' application status is disabled or undefined. Otherwise, no explicit return value is provided.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Update'], $_POST['clone_categories_id_to'])) {
      $Qproducts = $this->app->db->prepare('select *
                                              from :table_products
                                              where products_id = :products_id
                                             ');
      $Qproducts->bindInt(':products_id', $_GET['pID']);

      $Qproducts->execute();

      $sql_array = ['manufacturers_id' => (int)$Qproducts->valueInt('manufacturers_id')];
      $insert_array = ['products_id' => HTML::sanitize($_POST['clone_products_id'])];

      $this->app->db->save('products', $sql_array, $insert_array);
    }
  }
}