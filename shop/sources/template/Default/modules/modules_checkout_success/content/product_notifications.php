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

use ClicShopping\OM\html;
  use ClicShopping\OM\CLICSHOPPING;
  echo $form;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="card card-success">
    <div class="card-header"><?php echo CLICSHOPPING::getDef('module_checkout_success_product_notifications_text_notify_products'); ?></div>
    <div class="card-block">
      <div class="separator"></div>
      <div><p class="checkoutSuccessProductsNotifications"><?php echo $products_notifications; ?></p></div>
    </div>
    <div class="separator"></div>
    <div class="control-group">
      <div class="controls">
        <div class="buttonSet">
          <span class="float-md-right"><label for="buttonUpdate"><?php echo HTML::button(CLICSHOPPING::getDef('button_update'), null, null, 'info'); ?></label></span>
        </div>
      </div>
    </div>
  </div>
  </div>
<div class="separator"></div>
<?php
  echo $endform;

