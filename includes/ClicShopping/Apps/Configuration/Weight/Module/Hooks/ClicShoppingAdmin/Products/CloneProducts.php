<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Weight\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Weight\Weight as WeightApp;

class CloneProducts implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Weight')) {
      Registry::set('Weight', new WeightApp());
    }

    $this->app = Registry::get('Weight');
  }


  /**
   * Executes the method logic for handling product weight class updates based on the provided parameters.
   *
   * @return bool Returns false if the application weight module is not active or defined.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_WEIGHT_WE_STATUS') || CLICSHOPPING_APP_WEIGHT_WE_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Update'], $_POST['clone_categories_id_to'], $_GET['pID'])) {
      $Qproducts = $this->app->db->prepare('select *
                                              from :table_products
                                              where products_id = :products_id
                                             ');
      $Qproducts->bindInt(':products_id', $_GET['pID']);

      $Qproducts->execute();

      $sql_array = ['products_weight_class_id' => (int)$Qproducts->valueInt('products_weight_class_id')];
      $insert_array = ['products_id' => HTML::sanitize($_POST['clone_products_id'])];

      $this->app->db->save('products', $sql_array, $insert_array);
    }
  }
}