<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Groups')) {
      Registry::set('Groups', new GroupsApp());
    }

    $this->app = Registry::get('Groups');
  }

  private function removeGroups($id)
  {

    if (isset($_POST['remove_id']) && !empty($_POST['remove_id'])) {
      $this->app->db->delete('products_groups', ['products_id' => (int)$id]);
    }
  }

  public function execute()
  {
    if (isset($_POST['remove_id'])) {
      $id = HTML::sanitize($_POST['remove_id']);
      $this->removeGroups($id);
    }
  }
}