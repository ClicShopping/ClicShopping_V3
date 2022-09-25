<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\DateTime;
?>
<div class="col-md-<?php echo $content_width; ?>">
    <div class="separator"></div>
    <div class="page-title"><h3><?php echo CLICSHOPPING::getDef('module_account_customers_history_heading_title'); ?></h3></div>
    <div class="separator"></div>
    <div class="col-md-12"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_heading_description'); ?></strong></div>
    <div class="separator"></div>
  <div>
<?php
  if ($ordersTotalRow > 0) {
    foreach ($Qorders->fetchAll() as $order) {
      if (!empty($order['delivery_name'])) {
        $order_type = CLICSHOPPING::getDef('module_account_customers_history_order_shipped_to');
        $order_name = $order['delivery_name'];
      } else {
        $order_type = CLICSHOPPING::getDef('module_account_customers_history_order_billed_to');
        $order_name = $order['billing_name'];
      }
// ---------------------- ---------
// --- Display history number   -----
// ---------------------- --------
?>
      <div class="separator"></div>
      <div class="card">
        <div class="card-header">
          <div class="col-md-12">
            <div class="row">
              <span class="col-md-6"><?php echo '<strong>' . CLICSHOPPING::getDef('module_account_customers_history_order_number') . '</strong> ' . (int)$order['orders_id']; ?></span>
              <span class="col-md-6 text-end"><?php echo '<strong>' . CLICSHOPPING::getDef('module_account_customers_history_order_status') . '</strong> ' . $order['orders_status_name']; ?></span>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6"><?php echo '<strong>' . CLICSHOPPING::getDef('module_account_customers_history_order_date') . '</strong> ' .  DateTime::toLong($order['date_purchased']); ?></div>
              <div class="col-md-6 text-end"><strong><?php echo $order_type; ?></strong> <?php echo HTML::outputProtected($order_name); ?></div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-10"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_order_cost'); ?></strong> <?php echo strip_tags($order['order_total']); ?></div>
              <div class="col-md-2">
                <p class="float-end"><?php echo HTML::button(CLICSHOPPING::getDef('button_view'), null, CLICSHOPPING::link(null, (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'Account&HistoryInfo&order_id=' . (int)$order['orders_id']), 'info', null,'sm'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
<?php
    }
  } else {
?>
    <div class="separator"></div>
    <div class="alert alert-info" role="alert">
      <p><?php echo CLICSHOPPING::getDef('module_account_customers_history_no_purchases'); ?></p>
    </div>
<?php
  }
?>
    </div>
    <div class="separator"></div>
    <div class="clearfix"></div>
    <div class="col-md-12">
      <div class="col-md-6 pagenumber hidden-xs"><?php echo $Qorders->getPageSetLabel(CLICSHOPPING::getDef('text_display_number_of_items')); ?></div>
      <div class="col-md-12">
        <div class="float-end text-end pagenav">
          <ul class="pagination">
            <?php echo $Qorders->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y')), 'Shop'); ?>
          </ul>
        </div>
        <div class="float-end"><?php echo CLICSHOPPING::getDef('text_result_page'); ?></div>
      </div>
    </div>
<?php
  // ----------------------
  // --- Button   -----
  // ----------------------
?>
    <div class="control-group">
        <div class="buttonSet"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link(null, 'Account&Main'), 'primary'); ?></div>
    </div>
</div>
