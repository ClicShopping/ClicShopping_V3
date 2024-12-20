<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Manufacturers\Manufacturers as ManufacturersApp;

class CloneProducts implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Constructor method for initializing the Manufacturers application.
   *
   * Ensures the Manufacturers module is available in the Registry.
   * If it does not exist, it creates and registers a new ManufacturersApp instance.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Manufacturers')) {
      Registry::set('Manufacturers', new ManufacturersApp());
    }

    $this->app = Registry::get('Manufacturers');
  }


  /**
   * Executes the method logic to handle updates related to product manufacturers.
   *
   * If the application constant `CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS` is either undefined or set to 'False',
   * the method returns false and no further action is taken. Otherwise, if valid update and cloning parameters
   * (`Update` and `clone_categories_id_to`) are set, it processes a database operation to associate
   * a new manufacturer ID with a cloned product.
   *
   * @return bool Returns false if the application status constant is not defined or disabled;
   *              otherwise, executes the database logic without returning a value.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS') || CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Update'], $_POST['clone_categories_id_to'])) {
      $Qproducts = $this->app->db->prepare('select *
                                              from :table_products
                                              where products_id = :products_id
                                             ');
      $Qproducts->bindInt(':products_id', $_GET['pID']);

      $Qproducts->execute();

      $sql_array = ['manufacturers_id' => (int)HTML::sanitize($_POST['manufacturers_id'])];
      $insert_array = ['products_id' => HTML::sanitize($_POST['clone_products_id'])];

      $this->app->db->save('products', $sql_array, $insert_array);
    }
  }
}