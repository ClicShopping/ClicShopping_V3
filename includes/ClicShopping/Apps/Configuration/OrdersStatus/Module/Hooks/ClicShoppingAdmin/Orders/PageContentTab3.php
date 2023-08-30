<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatus\Module\Hooks\ClicShoppingAdmin\Orders;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\OrdersStatus\OrdersStatus as OrdersStatusApp;

class PageContentTab3 implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {

    if (!Registry::exists('OrdersStatus')) {
      Registry::set('OrdersStatus', new OrdersStatusApp());
    }

    $this->app = Registry::get('OrdersStatus');
    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Orders/page_content_tab3');
  }

  public function display(): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Orders = Registry::get('Orders');

    if (!\defined('CLICSHOPPING_APP_ORDERS_STATUS_OU_STATUS') || CLICSHOPPING_APP_ORDERS_STATUS_OU_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['oID'])) {
      $oID = HTML::sanitize($_GET['oID']);
      $orders_statuses = [];
      $orders_status_array = [];

      $QordersStatus = $this->app->db->prepare('select orders_status_id,
                                                          orders_status_name
                                                    from :table_orders_status
                                                    where language_id = :language_id
                                                    ');
      $QordersStatus->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QordersStatus->execute();

      while ($QordersStatus->fetch() !== false) {
        $orders_statuses[] = [
          'id' => $QordersStatus->valueInt('orders_status_id'),
          'text' => $QordersStatus->value('orders_status_name')
        ];

        $orders_status_array[$QordersStatus->valueInt('orders_status_id')] = $QordersStatus->value('orders_status_name');
      }

      $Qorders = $this->app->db->get('orders', 'orders_id', ['orders_id' => (int)$oID]);

      if ($Qorders->fetch()) {
        if (!Registry::exists('OrdersStatus')) {
          Registry::set('Order', new OrderAdmin($Qorders->valueInt('orders_id')));
        }

        $order = Registry::get('Order');

        $content = '<!-- order status start -->';
        $content .= '<span class="col-md-6">';
        $content .= '<span class="col-md-2"><strong>' . $CLICSHOPPING_Orders->getDef('entry_status') . '</strong></span> ';
        $content .= '<span class="col-md-4">';
        $content .= HTML::selectField('status', $orders_statuses, $order->info['orders_status']);
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