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
  <div class="col-md-12">
    <div class="form-group row">
      <label for="inputConfirmation" class="col-6 col-form-label"><?php echo CLICSHOPPING::getDef('modules_tell_a_friend_message_entry_antispam'); ?><span class="text-warning"><?php echo ' ' . $antispam; ?></span></label>
      <div class="col-md-5">
        <?php echo $simple_antispam; ?>
      </div>
    </div>
  </div>
</div>