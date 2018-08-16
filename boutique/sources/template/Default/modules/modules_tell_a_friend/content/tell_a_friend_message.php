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
  <div class="modulesTellAFriendMessagePageHeader">
    <h3><?php echo CLICSHOPPING::getDef('modules_tell_a_friend_message_title_friend_message'); ?></h3>
  </div>
  <div class="separator"></div>
  <div class="col-md-11"><?php echo $message ?></div>
  <div class="separator"></div>
  <div class="separator"></div>
  <div class="col-md-12">
    <div class="form-group row">
      <label for="inputConfirmation" class="col-6 col-form-label"><?php echo CLICSHOPPING::getDef('modules_tell_a_friend_message_entry_number_email_confirmation'); ?><span class="text-warning"><?php echo ' ' . $number_confirmation; ?></span></label>
      <div class="col-md-5">
        <?php echo $confirmation; ?>
      </div>
    </div>
  </div>

  <div class="separator"></div>
<?php
  // ----------------------
  // Confirmation Recaptcha
  // ----------------------
?>
  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <div class="col-md-6"><?php echo $google_recaptcha; ?></div>
      </div>
    </div>
  </div>
</div>