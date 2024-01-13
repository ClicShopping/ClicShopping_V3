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
use ClicShopping\OM\HTML;

?>
<div class="col-md-<?php echo $content_width; ?>" id="AccountProSuccess">
  <?php
  if (MEMBER == 'false') {
    ?>
    <div
      class="modulesCreateProSuccessPageHeader"><?php echo CLICSHOPPING::getDef('module_create_account_pro_success_text_account_created', ['url_support' => CLICSHOPPING::link(null, 'Info&Contact')]); ?></div>
    <?php
  } else {
    ?>
    <div
      class="modulesCreateProSuccessPageHeader"><?php echo CLICSHOPPING::getDef('module_create_account_pro_success_text_account_created_1', ['store_name' => STORE_NAME]); ?></div>
    <?php
  }
  ?>
  <div class="mt-1"></div>
  <div class="control-group">
    <div>
      <div class="buttonSet">
        <span
          class="float-end"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, $origin_href, 'success'); ?></span>
      </div>
    </div>
  </div>
  <div class="mt-1"></div>
</div>
