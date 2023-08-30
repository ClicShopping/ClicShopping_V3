<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatus\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\OrdersStatus\OrdersStatus as OrdersStatusApp;

class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('OrdersStatus')) {
      Registry::set('OrdersStatus', new OrdersStatusApp());
    }

    $this->app = Registry::get('OrdersStatus');
  }

  private function delete($id)
  {
    if (!\is_null($id)) {
      $this->app->db->delete('orders_status', ['language_id' => $id]);
    }
  }

  public function execute()
  {
    if (isset($_GET['DeleteConfirm'])) {
      $id = HTML::sanitize($_GET['lID']);
      $this->delete($id);
    }
  }
}