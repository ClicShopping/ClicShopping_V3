<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Configuration\OrdersStatus\Sites\ClicShoppingAdmin\Pages\Home\Actions\OrdersStatus;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('OrdersStatus');
    }

    public function execute()
    {

      $oID = HTML::sanitize($_GET['oID']);

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

      $this->app->redirect('OrdersStatus&' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''));
    }
  }