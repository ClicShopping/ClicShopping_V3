<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
<?php
  if (MEMBER == 'false') {
?>
    <div class="modulesCreateProSuccessPageHeader"><?php echo CLICSHOPPING::getDef('module_create_account_pro_success_text_account_created', ['url_support' => CLICSHOPPING::link('index.php','Info&Contact')]); ?></div>
<?php
  } else {
?>
    <div class="modulesCreateProSuccessPageHeader"><?php echo CLICSHOPPING::getDef('module_create_account_pro_success_text_account_created_1', ['store_name' => STORE_NAME]); ?></div>
<?php
  }
?>
  <div class="separator"></div>
  <div class="control-group">
    <div class="controls">
      <div class="buttonSet">
        <span class="float-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, $origin_href, 'success'); ?></span>
      </div>
    </div>
  </div>
</div>
