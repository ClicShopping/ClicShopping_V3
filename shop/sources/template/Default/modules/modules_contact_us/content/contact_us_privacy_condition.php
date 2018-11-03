<?php
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="col-md-12" id="RowContentPricacyCondition1">
    <div class="separator"></div>
    <div class="modulesContactUsTextPrivacy">
      <?php echo HTML::checkboxField('customer_agree_privacy', null, null, 'required aria-required="true"') . ' ' . CLICSHOPPING::getDef('text_privacy_conditions_agree'); ?><br />
      <?php echo CLICSHOPPING::getDef('text_privacy_conditions_description', ['store_name' => STORE_NAME, 'privacy_url' => CLICSHOPPING::link(SHOP_CODE_URL_CONFIDENTIALITY)]); ?>
    </div>
  </div>
</div>