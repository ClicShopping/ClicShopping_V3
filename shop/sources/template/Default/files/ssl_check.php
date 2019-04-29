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
        <div class="controls">
          <div class="buttonSet">
            <span class="float-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(null, 'Account&LogIn'), 'success'); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>