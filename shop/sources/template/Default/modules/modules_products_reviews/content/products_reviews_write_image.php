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
<div class="col-md-<?php echo $content_width; ?> float-md-right">
  <div class="separator"></div>
  <div class="float-md-right">
    <div class="text-md-center"><?php echo $reviews_image; ?></div>
    <div class="text-md-center">
      <div><?php echo $products_name; ?></div>
      <div><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $products_price; ?></div>
    </div>
  </div>
</div>