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
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div>
    <label for="inputComments" class="moduleCheckoutShippingCommentLabel">
      <h3><?php echo CLICSHOPPING::getDef('module_checkout_shipping_comment_table_heading_comments'); ?></h3>
    </label>
    <div class="col-md-12"><?php echo $comment_fields; ?></div>
  </div>
</div>
