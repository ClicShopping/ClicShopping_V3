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
  <div class="text-center shoppingCartInformationSaveText">
    <?php echo CLICSHOPPING::getDef('module_shopping_cart_delay_save_cart_message_information'); ?>
  </div>
  <div class="mt-1"></div>
</div>