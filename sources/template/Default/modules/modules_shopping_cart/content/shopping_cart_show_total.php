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
<div class="col-md-<?php echo $content_width; ?> <?php echo $position; ?>">
  <div class="separator"></div>
  <div class="shoppingCartSubTotal text-md-right"><?php echo CLICSHOPPING::getDef('module_shopping_cart_show_total_sub_total') . ' ' . $sub_total; ?></div>
</div>