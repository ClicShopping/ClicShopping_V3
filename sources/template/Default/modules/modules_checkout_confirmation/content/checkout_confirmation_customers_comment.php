<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;

?>
<div class="clearfix"></div>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="col-md-12">
    <div class="separator"></div>
    <div class="page-title moduleCheckoutConfirmationCustomersCommentPageHeader">
      <h3><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_customers_comment_heading_order_title'); ?></h3>
    </div>
    <div class="card moduleCheckoutConfirmationCustomersCommentCard">
      <div class="card-header moduleCheckoutConfirmationCustomersCommentCardHeader">
        <strong><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_customers_comment_heading_order_comments'); ?></strong><?php echo $edit_comment; ?>
      </div>
      <div class="card-block moduleCheckoutConfirmationCustomersCommentCardBlock">
        <div class="separator"></div>
        <?php echo $comment; ?>
      </div>
    </div>
  </div>
</div>
