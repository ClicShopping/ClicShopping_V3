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

if ($CLICSHOPPING_MessageStack->exists('main')) {
  echo $CLICSHOPPING_MessageStack->get('main');
}

require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

echo HTML::form('checkout_confirmation', $form_action_url, 'post', 'id="checkout_confirmation" role="form" onsubmit="return checkCheckBox(this)"');
?>
<section class="checkout_confirmation" id="checkout_confirmation">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-title"><h1><?php echo CLICSHOPPING::getDef('heading_title_Confirmation'); ?></h1></div>
      <div>
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_checkout_confirmation'); ?>
      </div>
    </div>
    <div class="separator"></div>
  </div>
</section>
</form>
