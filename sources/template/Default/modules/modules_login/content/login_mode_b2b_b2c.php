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

<div class="col-md-<?php echo $content_width . ' ' . MODULE_LOGIN_MODE_B2B_B2C_POSITION; ?>">
  <div class="separator"></div>
  <div class="text-start mainloginB2BB2C"><h2><?php echo CLICSHOPPING::getDef('text_open_account'); ?></h2></div>
  <div class="separator"></div>
  <div class="d-flex flex-wrap">
    <span class="col-md-6">
      <div class="card">
        <div class="card-header">
          <span class="mainloginB2BB2C"><?php echo CLICSHOPPING::getDef('text_b2b'); ?></span>
        </div>
        <div class="card-block">
          <div class="separator"></div>
          <div class="card-text">
           <div class="mainLogin"><?php echo CLICSHOPPING::getDef('text_intro_b2b'); ?></div>
            <div class="text-md-ight">
              <div class="control-group">
                <div class="separator"></div>
                <div>
                  <div
                    class="buttonSet text-end"><?php echo HTML::button(CLICSHOPPING::getDef('text_b2b'), null, CLICSHOPPING::link(null, 'Account&CreatePro'), 'success'); ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
    </span>
    <span class="col-md-6">
      <div class="card">
        <div class="card-header">
          <span class="mainloginB2BB2C"><?php echo CLICSHOPPING::getDef('text_b2c'); ?></span>
        </div>
        <div class="card-block">
          <div class="separator"></div>
          <div class="card-text">
            <div class="mainLogin"><?php echo CLICSHOPPING::getDef('text_intro_b2c'); ?></div>
            <div class="text-end">
              <div class="control-group">
                <div class="separator"></div>
                <div>
                  <div
                    class="buttonSet text-end"><?php echo HTML::button(CLICSHOPPING::getDef('text_b2c'), null, CLICSHOPPING::link(null, 'Account&Create'), 'primary'); ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
    </span>
  </div>
</div>
