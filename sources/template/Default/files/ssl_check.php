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

require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

// ----------------------
// --- Message  -----
// ----------------------
?>
<section class="ssl" id="ssl">
  <div class="contentContainer">
    <div class="contentText">
      <div class="card card-danger">
        <div class="card-header"><?php echo CLICSHOPPING::getDef('box_information_heading'); ?></div>
        <div class="card-block">
          <div class="separator"></div>
          <?php echo CLICSHOPPING::getDef('box_information'); ?>
        </div>
      </div>
      <div class="separator"></div>
      <div class="card card-danger">
        <div class="card-block">
          <div class="separator"></div>
          <?php echo CLICSHOPPING::getDef('text_information'); ?>
        </div>
      </div>

      <?php
      // ----------------------
      // --- Button  -----
      // ----------------------
      ?>
      <div class="separator"></div>
      <div class="control-group">
        <div>
          <div class="buttonSet">
            <span class="float-end"><label
                for="buttonContinue"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(null, 'Account&LogIn'), 'success'); ?></label></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>