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
  <div class="card">
    <div class="card-header moduleCheckoutPaymentCommentPageHeader">
      <span><h3><?php echo CLICSHOPPING::getDef('module_checkout_payment_comment_table_heading_comments'); ?></h3></span>
    </div>
    <div class="card-block">
      <div class="separator"></div>
      <div class="card-text moduleCheckoutPaymentAgreementText">
        <?php echo $comment_fields; ?>
      </div>
    </div>
  </div>
  <div class="separator"></div>
</div>