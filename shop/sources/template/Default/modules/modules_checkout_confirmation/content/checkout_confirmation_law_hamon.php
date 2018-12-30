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
<div class="col-md-<?php echo $content_width; ?>">
  <div class="col-md-12">
    <div class="separator"></div>
<script>
  function checkCheckBox(f){
    if (f.agree.checked === false )
    {
      alert('<?php echo CLICSHOPPING::getDef('module_checkout_confirmation_law_hamon_text_error_agreement'); ?>');
      return false;
    }else
      return true;
  }
</script>
    <div class="card">
      <div class="card-header">
        <span class="alert-warning float-md-right" role="alert"><?php echo CLICSHOPPING::getDef('form_required_information'); ?></span>
        <span><h2><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_law_hamon_text_title'); ?></h2></span>
      </div>
      <div class="card-block">
        <div class="card-text">
          <div class="checkoutConfirmationLawHamon"><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_law_hamon_text_conditions'); ?>&nbsp;</div>
          <div class="text-md-right"><?php echo $agree_checkbox; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
