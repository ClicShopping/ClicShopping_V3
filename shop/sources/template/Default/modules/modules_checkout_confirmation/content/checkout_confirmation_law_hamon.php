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
  use ClicShopping\OM\HTML;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="col-md-12">
    <div class="separator"></div>
    <div class="card">
      <div class="card-header">
        <span class="alert-warning float-md-right" role="alert"><?php echo CLICSHOPPING::getDef('form_required_information'); ?></span>
        <span><h2><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_law_hamon_text_title'); ?></h2></span>
      </div>
      <div class="card-block">
        <div class="separator"></div>
        <div class="card-text">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="separator"></div>
              <div class="checkoutConfirmationLawHamon"><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_law_hamon_text_conditions'); ?></div>
              <label class="switch">
                <?php echo HTML::checkboxField('agree','1', false, 'id="agree" required aria-required="true" class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
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