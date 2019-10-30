<?php
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="<?php echo $text_position; ?> col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="col-md-12">
    <div class="separator"></div>
    <div class="modulesProductsReviewsCustomerAgreement">

      <ul class="list-group list-group-flush">
        <li class="list-group-item-slider">
          <?php echo CLICSHOPPING::getDef('text_privacy_conditions_description', ['store_name' => STORE_NAME, 'privacy_url' => CLICSHOPPING::link(SHOP_CODE_URL_CONFIDENTIALITY)]); ?>
          <div class="separator"></div>
          <?php echo CLICSHOPPING::getDef('text_privacy_conditions_agree'); ?>
          <label class="switch">
            <?php echo HTML::checkboxField('customer_agree_privacy', null, null, 'required aria-required="true" class="success"'); ?>
            <span class="slider"></span>
          </label>
        </li>
      </ul>
    </div>
  </div>
</div>
