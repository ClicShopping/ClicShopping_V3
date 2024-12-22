<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
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
  public mixed $app;

  /**
   * Initializes the Groups application instance by checking if it exists in the registry.
   * If not found, it sets a new instance of GroupsApp in the registry and retrieves it.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Groups')) {
      Registry::set('Groups', new GroupsApp());
    }

    $this->app = Registry::get('Groups');
  }

  /**
   * Removes groups associated with a specific product ID from the database.
   *
   * @param int $id The ID of the product whose groups are to be removed.
   * @return void
   */
  private function removeGroups($id)
  {

    if (isset($_POST['remove_id']) && !empty($_POST['remove_id'])) {
      $this->app->db->delete('products_groups', ['products_id' => (int)$id]);
    }
  }

  /**
   * Executes the action of removing a group based on the POST parameter 'remove_id'.
   *
   * @return void
   */
  public function execute()
  {
    if (isset($_POST['remove_id'])) {
      $id = HTML::sanitize($_POST['remove_id']);
      $this->removeGroups($id);
    }
  }
}