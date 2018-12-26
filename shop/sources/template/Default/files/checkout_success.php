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

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  if ( $CLICSHOPPING_MessageStack->exists('checkout_success') ) {
?>
    <div class="alert-success"><?php echo $CLICSHOPPING_MessageStack->get('checkout_success'); ?></div>
    <div class="separator"></div>
<?php
  }
?>
<section class="checkout_success" id="checkout_success">
  <div class="contentContainer">
    <div class="contentText">
      <div class="clearfix"></div>
      <div class="separator"></div>
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_checkout_success'); ?>

      <div class="control-group">
        <div class="controls">
          <div class="buttonSet">
            <span class="float-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(), 'success'); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>