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
<div class="col-md-<?php echo $content_width; ?>" id="AccountSuccess">
  <div class="page-title modulesCreateAccountSuccess">
    <h3><?php echo CLICSHOPPING::getDef('module_create_account_success_text_account_created'); ?></h3></div>
  <div class="separator"></div>
  <div>
    <div class="control-group">
      <div>
        <div class="buttonSet">
          <span
            class="float-end"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, $origin_href, 'success'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
</div>
