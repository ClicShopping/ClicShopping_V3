<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Module\Hooks\ClicShoppingAdmin\Orders;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\OrdersStatusInvoice\OrdersStatusInvoice as OrdersStatusInvoiceApp;
use ClicShopping\Apps\Orders\Orders\Classes\ClicShoppingAdmin\OrderAdmin;

class PageContentTab3 implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('OrdersStatusInvoice')) {
      Registry::set('OrdersStatusInvoice', new OrdersStatusInvoiceApp());
    }

    $this->app = Registry::get('OrdersStatusInvoice');

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Orders/page_content_tab3');
  }

  public function display()
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Orders = Registry::get('Orders');

    if (!\defined('CLICSHOPPING_APP_ORDERS_STATUS_INVOICE_OI_STATUS') || CLICSHOPPING_APP_ORDERS_STATUS_INVOICE_OI_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['oID'])) {
      $oID = HTML::sanitize($_GET['oID']);

      $orders_invoice_statuses = [];
      $orders_status_invoice_array = [];

      $QordersStatusInvoice = $CLICSHOPPING_Orders->db->prepare('select orders_status_invoice_id,
                                                                          orders_status_invoice_name
                                                                   from :table_orders_status_invoice
                                                                   where language_id = :language_id
                                                                  ');
      $QordersStatusInvoice->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QordersStatusInvoice->execute();

      while ($QordersStatusInvoice->fetch()) {
        $orders_invoice_statuses[] = [
          'id' => $QordersStatusInvoice->valueInt('orders_status_invoice_id'),
          'text' => $QordersStatusInvoice->value('orders_status_invoice_name')
        ];

        $orders_status_invoice_array[$QordersStatusInvoice->valueInt('orders_status_invoice_id')] = $QordersStatusInvoice->value('orders_status_invoice_name');
      }

      $Qorders = $this->app->db->get('orders', 'orders_id', ['orders_id' => (int)$oID]);

      if ($Qorders->fetch()) {
        if (!Registry::exists('OrdersStatus')) {
          Registry::set('Order', new OrderAdmin($Qorders->valueInt('orders_id')));
        }

        $order = Registry::get('Order');

        $content = '<!-- order status start -->';
        $content .= '<div class="col-md-6">';
        $content .= '<span class="col-md-2"><strong>' . $CLICSHOPPING_Orders->getDef('entry_status_invoice') . '</strong></span> ';
        $content .= '<span class="col-md-4">';
        $content .= HTML::selectField('status_invoice', $orders_invoice_statuses, $order->info['orders_status_invoice']);
        $content .= '</span>';
        $content .= '</span>';
        $content .= '<!-- order status end -->';

        $output = <<<EOD
<!-- ######################## -->
<!--  Start order status     -->
<!-- ######################## -->
<script>
$('#entryStatus').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End order status      -->
<!-- ######################## -->
EOD;
        return $output;
      }
    }
  }
}