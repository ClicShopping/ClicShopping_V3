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

use ClicShopping\OM\CLICSHOPPING;

  echo $form;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="form-group">
    <h3><?php echo CLICSHOPPING::getDef('my_newsletter_title'); ?></h3>
    <div class="contentText">
      <div class="checkbox">
        <label>
          <?php echo $newsletter_checkbox; ?>
          <strong><?php echo CLICSHOPPING::getDef('module_account_customers_newsletter'); ?></strong><br /><?php echo CLICSHOPPING::getDef('module_account_customers_newsletters_description'); ?>
        </label>
      </div>
    </div>
  </div>

  <div class="col-md-12">
    <div class="separator"></div>
    <div class="control-group">
      <div class="controls">
        <div class="buttonSet">
          <span class="col-md-2"><?php echo $button_back; ?></span>
          <span class="col-md-2 float-md-right text-md-right"><?php echo $button_process; ?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  echo $endform;