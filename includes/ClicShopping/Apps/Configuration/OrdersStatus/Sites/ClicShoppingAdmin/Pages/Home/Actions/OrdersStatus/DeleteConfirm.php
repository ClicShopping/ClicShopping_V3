<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\OrdersStatus\Sites\ClicShoppingAdmin\Pages\Home\Actions\OrdersStatus;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('OrdersStatus');
    $this->hooks = Registry::get('Hooks');
  }

  public function execute()
  {

    if (isset($_GET['oID'])) {
      $oID = HTML::sanitize($_GET['oID']);
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      $Qstatus = $this->app->db->get('configuration', 'configuration_value', ['configuration_key' => 'DEFAULT_ORDERS_STATUS_ID']);

      if ($Qstatus->value('configuration_value') == $oID) {
        $this->app->db->save('configuration', [
          'configuration_value' => ''
        ], [
            'configuration_key' => 'DEFAULT_ORDERS_STATUS_ID'
          ]
        );
      }

      $this->app->db->delete('orders_status', ['orders_status_id' => (int)$oID]);
      $this->hooks->call('OrdersStatus', 'DeleteConfirmOrdersStatus');

      Cache::clear('configuration');

      $this->app->redirect('OrdersStatus&page=' . $page);
    }
  }
}