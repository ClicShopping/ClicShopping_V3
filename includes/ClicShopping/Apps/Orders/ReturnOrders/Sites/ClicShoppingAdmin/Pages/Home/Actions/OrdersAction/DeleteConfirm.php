<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Orders\ReturnOrders\Sites\ClicShoppingAdmin\Pages\Home\Actions\OrdersAction;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('ReturnOrders');
      $this->hooks = Registry::get('Hooks');
    }

    public function execute()
    {
      if (isset($_GET['oID'])) {
        $oID = HTML::sanitize($_GET['oID']);
        $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

        $this->app->db->delete('return_orders_action', ['return_action_id' => (int)$oID]);

        $this->hooks->call('ReturnOrders', 'DeleteConfirmOrdersAction');

        Cache::clear('configuration');

        $this->app->redirect('OrdersAction&page=' . $page);
      }
    }
  }