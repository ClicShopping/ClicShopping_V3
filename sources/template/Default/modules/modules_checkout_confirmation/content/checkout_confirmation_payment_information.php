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
  <div class="col-md-12">
    <div class="separator"></div>
    <div class="card moduleCheckoutConfirmationPaymentInformationCard">
      <div class="card-header moduleCheckoutConfirmationPaymentInformationCardHeader"><strong><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_payment_information_heading_payment_information'); ?></strong></div>
      <div class="card-block moduleCheckoutConfirmationPaymentInformationCardBlock">
        <div class="separator"></div>
        <table class="table moduleCheckoutConfirmationPaymentInformationCardTable">
          <?php echo $data; ?>
        </table>
      </div>
    </div>
  </div>
</div>
