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
<div class="clearfix"></div>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="col-md-12">
    <div class="mt-1"></div>
    <div class="card">
      <div class="card-header">
        <span class="alert-warning float-end"
              role="alert"><?php echo CLICSHOPPING::getDef('form_required_information'); ?></span>
        <span><h2><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_law_hamon_text_title'); ?></h2></span>
      </div>
      <div class="card-block">
        <div class="mt-1"></div>
        <div class="card-text">
          <ul class="list-group list-group-flush">
            <li class="list-group-item-slider">
              <div class="mt-1"></div>
              <div
                class="checkoutConfirmationLawHamon"><?php echo CLICSHOPPING::getDef('module_checkout_confirmation_law_hamon_text_conditions'); ?></div>
              <label class="switch">
                <?php echo HTML::checkboxField('agree', '1', false, 'id="agree" required aria-required="true" class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
