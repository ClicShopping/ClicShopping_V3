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
  <div class="card">
    <div class="card-header"><?php echo CLICSHOPPING::getDef('module_checkout_success_text_success'); ?></div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div><?php echo CLICSHOPPING::getDef('module_checkout_success_text_thanks_for_shopping', ['store_name' => STORE_NAME]); ?></div>
      <div class="separator"></div>
      <div class="hr"></div>
      <div class="m-4 ClicShoppingCheckoutSuccessText">
        <span><?php echo $text_info; ?></span>
        <div class="hr"></div>
        <span><?php echo $contact; ?></span>
      </div>
    </div>
  </div>
  <div class="separator"></div>
</div>
