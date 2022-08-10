<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <span class="col-md-3 float-start">
    <div class="card moduleCheckoutConfirmationOrderTotalCard">
      <div class="card-header moduleCheckoutConfirmationOrderTotalHeader"><strong><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_order_total_heading_details'); ?></strong></div>
    </div>
    <div class="separator"></div>
  </span>
  <div class="col-md-9 float-end">
    <table width="100%" class="moduleCheckoutConfirmationOrderTotalTable">
      <?php echo $order_total; ?>
    </table>
    <div class="separator"></div>
  </div>
</div>
