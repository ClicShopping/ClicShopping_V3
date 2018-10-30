<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <span class="col-md-6 float-md-left">
    <div clas="moduleCheckoutShippingAddressDestination"><?php echo CLICSHOPPING::getDef('module_checkout_shipping_address_text_choose_shipping_destination'); ?></div>
    <div class="separator"></div>
    <div class="moduleCheckoutShippingAddressButton"><?php echo $address_button ?></div>
    <div class="separator"></div>
  </span>

  <div class="separator"></div>
  <span class="col-md-6 float-md-right">
    <div class="card moduleCheckoutShippingAddressCard">
      <div class="card-header moduleCheckoutShippingAddressCardHeader"><h3><?php echo  CLICSHOPPING::getDef('module_checkout_shipping_address_title_shipping_address'); ?></h3></div>
      <div class="card-block moduleCheckoutShippingAddressCardBlock">
        <?php echo $address_send_to; ?>
      </div>
    </div>
    <div class="separator"></div>
  </span>
</div>

