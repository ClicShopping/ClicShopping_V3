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

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width . ' ' . MODULE_LOGIN_MODE_B2B_POSITION; ?>">
  <div class="card">
    <div class="card-header">
      <h2><span><?php echo CLICSHOPPING::getDef('heading_title_b2b'); ?></span></h2>
    </div>
    <div class="card-block">
      <div class="separator"></div>
      <div class="card-text">
        <div class="mainLogin"><?php echo CLICSHOPPING::getDef('text_intro_b2b'); ?></div>
        <div class="text-rmd-ight">
          <div class="control-group">
            <div class="separator"></div>
            <div class="controls">
              <div class="buttonSet text-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_retailers'), null, CLICSHOPPING::link(null, 'Account&CreatePro'), 'primary'); ?></div>
            </div>
            <div class="separator"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
