<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */
  use ClicShopping\OM\CLICSHOPPING;

  if ($CLICSHOPPING_MessageStack->exists('main')) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="create_account" id="create_account">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-title"><h1><?php echo CLICSHOPPING::getDef('heading_title_create_account'); ?></h1></div>
      <div class="separator"></div>
      <div><?php echo $CLICSHOPPING_Template->getBlocks('modules_create_account'); ?></div>
    </div>
  </div>
</section>
