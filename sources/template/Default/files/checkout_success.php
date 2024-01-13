<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;

require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

if ($CLICSHOPPING_MessageStack->exists('checkout_success')) {
  echo $CLICSHOPPING_MessageStack->get('main');
}
?>
<div class="mt-1"></div>
<section class="checkout_success" id="checkout_success">
  <div class="contentContainer">
    <div class="contentText">
      <div class="mt-1"></div>
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_checkout_success'); ?>
      <div class="control-group">
        <div>
          <div class="buttonSet">
            <span class="float-end"><label
                for="buttonContinue"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(), 'success'); ?></label></span>
          </div>
        </div>
      </div>
      <div class="mt-1"></div>
    </div>
  </div>
</section>