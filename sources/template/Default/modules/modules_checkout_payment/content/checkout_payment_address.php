<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;

?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="mt-1"></div>
  <span class="col-md-6 float-start">
    <div
      class="moduleCheckoutPaymentAddressDestination"><?php echo CLICSHOPPING::getDef('module_checkout_payment_address_text_choose_payment_destination'); ?></div>
    <div class="mt-1"></div>
    <div class="moduleCheckoutPaymentAddressButton"><?php echo $address_button ?></div>
    <div style="padding-top:4rem;"></div>
  </span>
  <div class="mt-1"></div>
  <span class="col-md-6 float-end">
    <div class="card moduleCheckoutPaymentAddressCard">
      <div
        class="card-header moduleCheckoutPaymentAddressCardHeader"><h3><?php echo CLICSHOPPING::getDef('module_checkout_payment_address_title_payment_address'); ?></h3></div>
      <div class="card-block moduleCheckoutPaymentAddressCardBlock">
        <div class="mt-1"></div>
        <?php echo $address_billto; ?>
      </div>
    </div>
    <div class="mt-1"></div>
  </span>
</div>
<div class="clearfix"></div>