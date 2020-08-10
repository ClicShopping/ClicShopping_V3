<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>

  <div class="d-flex flex-wrap">
<?php
  if ($CLICSHOPPING_Order->delivery !== false) {
?>
    <span class="col-md-6">
      <div class="card-header"><?php echo '<strong>' . CLICSHOPPING::getDef('module_account_customers_history_info_address_heading_delivery_address')  . '</strong>'; ?></div>
      <div class="card-block">
        <div class="separator"></div>
        <?php echo $address_delivery; ?>
      </div>
    </span>
<?php
    if (!empty($CLICSHOPPING_Order->info['shipping_method'])) {
?>
    <span class="col-md-6">
      <div class="card-header"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_address_heading_shipping_method'); ?></strong></div>
      <div class="card-block">
        <div class="separator"></div>
        <?php echo $shipping_method; ?>
      </div>
    </span>
<?php
    }
  }
?>
    <span class="col-md-6">
      <div>
        <div class="card-header"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_address_heading_billing_address'); ?></strong></div>
        <div class="card-block">
          <div class="separator"></div>
          <?php echo $billing_address; ?></td>
        </div>
      </div>
    </span>
  </div>
  <div class="col-md-6">
    <div class="card-header"><strong><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_address_heading_payment_method'); ?></strong></div>
    <div class="card-block">
      <div class="separator"></div>
      <?php echo $payment_method; ?>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="separator"></div>
  <div class="hr"></div>
</div>

