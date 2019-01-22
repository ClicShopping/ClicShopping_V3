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
<?php
  echo $process_button;
?>
  </div>
</div>

<script>
  $('form[name="checkout_confirmation"]').submit(function() {
    $('form[name="checkout_confirmation"] button[data-button="payNow"]').html('<?php echo addslashes(CLICSHOPPING::getDef('module_checkout_confirmation_process_order_button_pay')); ?>').prop('disabled', true);
  });
</script>