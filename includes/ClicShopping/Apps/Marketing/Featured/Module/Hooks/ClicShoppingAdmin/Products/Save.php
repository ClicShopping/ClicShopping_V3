<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Featured\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Featured\Featured as FeaturedApp;

class Save implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Featured')) {
      Registry::set('Featured', new FeaturedApp());
    }

    $this->app = Registry::get('Featured');
  }

  /**
   * @param int $id
   */
  private function saveProductsFeatured(int $id): void
  {
    if (!empty($_POST['products_featured'])) {
      $insert_array = [
        'products_id' => (int)$id,
        'products_featured_date_added' => 'now()',
        'status' => 1,
        'customers_group_id' => 0
      ];

      $this->app->db->save('products_featured', $insert_array);
    }
  }

  private function save(int $id): void
  {
    $this->saveProductsFeatured($id);
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_FEATURED_FE_STATUS') || CLICSHOPPING_APP_FEATURED_FE_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['pID'])) {
      $id = HTML::sanitize($_GET['pID']);
      $this->save($id);
    } else {
      if (!empty($_POST['products_featured'])) {
        $Qproducts = $this->app->db->prepare('select products_id
                                                from :table_products
                                                order by products_id desc
                                                limit 1
                                               ');
        $Qproducts->execute();

        $insert_array = [
          'products_id' => $Qproducts->valueInt('products_id'),
          'products_featured_date_added' => 'now()',
          'status' => 1,
          'customers_group_id' => 0
        ];

        $this->app->db->save('products_featured', $insert_array);
      }
    }
  }
}