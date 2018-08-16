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
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="card">
    <div class="card-header moduleCheckoutPaymentAgreementHeading">
      <span><h3><?php echo CLICSHOPPING::getDef('module_checkout_payment_agreement_table_heading_conditions'); ?></h3></span>
    </div>
    <div class="card-block">
      <div class="card-text moduleCheckoutPaymentAgreementText">
        <?php echo CLICSHOPPING::getDef('module_checkout_payment_agreement_text_conditions_confirm', ['shop_code_url_conditions_vente' => CLICSHOPPING::link(SHOP_CODE_URL_CONDITIONS_VENTE)]). '<br /><br /> '; ?>
        <?php echo CLICSHOPPING::getDef('module_checkout_payment_agreement_text_conditions_description') . ' ' . $checkbox_aggreement;?>
      </div>
    </div>
  </div>
</div>
