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
<div class="col-md-<?php echo $content_width; ?> <?php echo $position; ?>">
  <div class="mt-1"></div>
  <div
    class="shoppingCartSubTotal text-end"><?php echo CLICSHOPPING::getDef('module_shopping_cart_show_total_sub_total') . ' ' . $sub_total; ?></div>
</div>