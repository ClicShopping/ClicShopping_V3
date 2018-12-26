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
<div class="col-md-<?php echo $content_width; ?>" id="RowContentSuccess1">
  <div class="modulesContactUsSuccess">
    <?php echo CLICSHOPPING::getDef('modules_contact_us_success_text_success', ['store_name' => STORE_NAME]); ?>
  </div>
  <div class="separator"></div>
  <div class="control-group">
    <div class="controls">
      <div class="buttonSet">
        <span class="float-md-right"><?php echo $button_process; ?></span>
      </div>
    </div>
  </div>
</div>