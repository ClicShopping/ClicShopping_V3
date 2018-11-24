<?php
  use ClicShopping\OM\CLICSHOPPING;
?>

<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="separator"></div>
  <div class="stepwizard">
    <div class="stepwizard-row">
      <div class="stepwizard-step">
        <button type="button" class="btn btn-primary btn-circle">1</button>
        <p><?php echo CLICSHOPPING::getDef('text_shopping_cart_checkout_step1'); ?></p>
      </div>
      <div class="stepwizard-step">
        <button type="button" class="btn btn-secondary btn-circle">2</button>
        <p><?php echo CLICSHOPPING::getDef('text_shopping_cart_checkout_step2'); ?></p>
      </div>
      <div class="stepwizard-step">
        <button type="button" class="btn btn-secondary btn-circle">3</button>
        <p><?php echo CLICSHOPPING::getDef('text_shopping_cart_checkout_step3'); ?></p>
      </div>
      <div class="stepwizard-step">
        <button type="button" class="btn btn-secondary btn-circle">4</button>
        <p><?php echo CLICSHOPPING::getDef('text_shopping_cart_checkout_step4'); ?></p>
      </div>
      <div class="stepwizard-step">
        <button type="button" class="btn btn-secondary btn-circle">5</button>
        <p><?php echo CLICSHOPPING::getDef('text_shopping_cart_checkout_step5'); ?></p>
      </div>
    </div>
  </div>
</div>