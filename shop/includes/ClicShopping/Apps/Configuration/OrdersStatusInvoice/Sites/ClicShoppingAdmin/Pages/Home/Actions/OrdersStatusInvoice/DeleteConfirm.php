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


  namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Sites\ClicShoppingAdmin\Pages\Home\Actions\OrdersStatusInvoice;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('OrdersStatusInvoice');
    }

    public function execute()
    {
      $oID = HTML::sanitize($_GET['oID']);

      $Qstatus = $this->app->db->get('configuration', 'configuration_value', ['configuration_key' => 'DEFAULT_ORDERS_STATUS_INVOICE_ID']);

      if ($Qstatus->value('configuration_value') == $oID) {
        $this->app->db->save('configuration', [
          'configuration_value' => ''
        ], [
            'configuration_key' => 'DEFAULT_ORDERS_STATUS_INVOICE_ID'
          ]
        );
      }

      $this->app->db->delete('orders_status_invoice', ['orders_status_invoice_id' => (int)$oID]);

      $this->app->redirect('OrdersStatusInvoice&' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''));
    }
  }