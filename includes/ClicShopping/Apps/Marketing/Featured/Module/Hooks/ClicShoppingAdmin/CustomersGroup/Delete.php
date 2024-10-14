<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Featured\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Featured\Featured as FeaturedApp;

class Delete implements \ClicShopping\OM\Modules\HooksInterface
{
  private mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Featured')) {
      Registry::set('Featured', new FeaturedApp());
    }

    $this->app = Registry::get('Featured');
  }

  /**
   * @param int $group_id
   */
  private function delete(int $group_id): void
  {
    $QProductsFeaturedCustomersId = $this->app->db->prepare('select count(customers_group_id) as count
                                                               from :table_products_featured
                                                               where customers_group_id = :customers_group_id
                                                               ');
    $QProductsFeaturedCustomersId->bindInt(':customers_group_id', (int)$group_id);
    $QProductsFeaturedCustomersId->execute();

    if ($QProductsFeaturedCustomersId->valueInt('count') > 0) {
      $Qdelete = $this->app->db->prepare('delete
                                             from :table_products_featured
                                             where customers_group_id = :customers_group_id
                                             ');
      $Qdelete->bindInt(':customers_group_id', (int)$group_id);
      $Qdelete->execute();
    }
  }

  public function execute()
  {
    if (isset($_GET['Delete'])) {
      $id = HTML::sanitize($_GET['cID']);
      $this->delete($id);
    }
  }
}