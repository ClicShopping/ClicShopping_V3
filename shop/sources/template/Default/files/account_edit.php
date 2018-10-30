<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  if ( $CLICSHOPPING_MessageStack->exists('account_edit') ) {
   echo $CLICSHOPPING_MessageStack->get('account_edit');
  }

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="account_edit" id="account_edit">
  <div class="contentContainer">
    <div class="contentText">
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_account_customers'); ?>
    </div>
  </div>
</section>
