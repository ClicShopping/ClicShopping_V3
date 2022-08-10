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

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="create_account_success" id="create_account_success">
  <div class="contentContainer">
    <div class="contentText">
     <div class="page-title"><h1><?php echo CLICSHOPPING::getDef('heading_title'); ?></h1></div>
     <div class="separator"></div>
     <div><?php echo $CLICSHOPPING_Template->getBlocks('modules_create_account'); ?></div>
    </div>
  </div>
</section>
