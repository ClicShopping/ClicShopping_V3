<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Communication\PageManager\PageManager as PageManagerApp;

class Delete implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('PageManager')) {
      Registry::set('PageManager', new PageManagerApp());
    }

    $this->app = Registry::get('PageManager');
  }

  private function delete(int $group_id): void
  {
    $QpageManagerCustomersId = $this->app->db->prepare("select count(customers_group_id) as count
                                                           from :table_pages_manager
                                                           where customers_group_id = :customers_group_id
                                                         ");
    $QpageManagerCustomersId->bindInt(':customers_group_id', (int)$group_id);
    $QpageManagerCustomersId->execute();

    if ($QpageManagerCustomersId->valueInt('count') > 0) {
      // delete all page manager
      $Qdelete = $this->app->db->prepare('delete
                                                        from :table_pages_manager
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