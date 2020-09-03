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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('main')) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  echo HTML::form('checkout_shipping', CLICSHOPPING::link(null, 'Checkout&Shipping&Process'), 'post', 'class="form-inline" role="form" id="checkout_shipping"', ['tokenize' => true, 'action' => 'process']);
?>
<section class="checkout_shipping" id="checkout_shipping">
  <div class="contentContainer">
    <div class="contentText">
      <div class="separator"></div>
      <div class="page-title"><h1><?php echo CLICSHOPPING::getDef('heading_title_checkout_shipping'); ?></h1></div>
      <div class="form-group">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_checkout_shipping'); ?>
      </div>
      <div class="separator"></div>
    </div>
  </div>
</section>
</form>