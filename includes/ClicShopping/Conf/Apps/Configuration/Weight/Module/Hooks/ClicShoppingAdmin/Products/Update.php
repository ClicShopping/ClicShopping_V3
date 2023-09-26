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

class Update implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Weight')) {
      Registry::set('Weight', new WeightApp());
    }

    $this->app = Registry::get('Weight');
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_WEIGHT_WE_STATUS') || CLICSHOPPING_APP_WEIGHT_WE_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Update'], $_GET['pID'])) {
      $id = HTML::sanitize($_GET['pID']);

      $sql_data_array = ['products_weight_class_id' => (int)HTML::sanitize($_POST['products_weight_class_id'])];

      $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
    }
  }
}