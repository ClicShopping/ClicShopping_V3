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
use ClicShopping\OM\HTML;

?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="card">
    <div class="card-header moduleCheckoutPaymentAgreementHeading">
      <span><h3><?php echo CLICSHOPPING::getDef('module_checkout_payment_agreement_table_heading_conditions'); ?></h3></span>
    </div>
    <div class="card-block">
      <div class="separator"></div>
      <div class="card-text moduleCheckoutPaymentAgreementText">
        <ul class="list-group list-group-flush">
          <li class="list-group-item-slider">
            <?php echo CLICSHOPPING::getDef('module_checkout_payment_agreement_text_conditions_confirm', ['shop_code_url_conditions_vente' => CLICSHOPPING::link(SHOP_CODE_URL_CONDITIONS_VENTE)]); ?>
            <div class="separator"></div>
            <?php echo CLICSHOPPING::getDef('module_checkout_payment_agreement_text_conditions_description'); ?>
            <label class="switch">
              <?php echo HTML::checkboxField('conditions', '1', false, 'id="conditions" required aria-required="true" class="success"'); ?>
              <span class="slider"></span>
            </label>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
