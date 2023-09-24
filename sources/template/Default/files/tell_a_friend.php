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
use ClicShopping\OM\Registry;

$CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
$CLICSHOPPING_Template = Registry::get('Template');
$CLICSHOPPING_MessageStack = Registry::get('MessageStack');

if ($CLICSHOPPING_MessageStack->exists('main')) {
  echo $CLICSHOPPING_MessageStack->get('main');
}

require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

echo HTML::form('email_friend', CLICSHOPPING::link(null, 'Products&TellAFriend&Process&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'post', 'id="tell_a_friend"', ['tokenize' => true, 'action' => 'process']);
?>
<section class="tell_a_friend" id="tell_a_friend">
  <div class="contentContainer">
    <div class="contenttext">
      <div class="col-md-12">
        <div class="page-title"><h1><?php echo CLICSHOPPING::getDef('heading_title_friends'); ?></h1></div>
        <div class="inputRequirement float-end"><?php echo CLICSHOPPING::getDef('form_required_information'); ?></div>
      </div>
      <div class="separator"></div>
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_tell_a_friend'); ?>
    </div>
  </div>
</section>
</form>