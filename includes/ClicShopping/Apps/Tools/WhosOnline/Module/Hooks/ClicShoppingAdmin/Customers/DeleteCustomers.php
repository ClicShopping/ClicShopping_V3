<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\WhosOnline\Module\Hooks\ClicShoppingAdmin\Customers;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\WhosOnline\WhosOnline as WhosOnlineApp;

class DeleteCustomers implements \ClicShopping\OM\Modules\HooksInterface
{
  private mixed $app;

  public function __construct()
  {
    if (!Registry::exists('WhosOnline')) {
      Registry::set('WhosOnline', new WhosOnlineApp());
    }

    $this->app = Registry::get('WhosOnline');
  }

  /**
   * @param int $group_id
   */
  private function deleteCustomer(int $id): void
  {
    $this->app->db->delete('whos_online', ['customer_id' => $id]);
  }

  public function execute()
  {
    if (isset($_GET['DeleteAll'])) {
      if (isset($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {
          $this->deleteCustomer($id);
        }
      } else {
        $id = HTML::sanitize($_POST['id']);
        $this->deleteCustomer($id);
      }
    }
  }
}