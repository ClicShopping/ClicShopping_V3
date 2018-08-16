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

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  if ( $CLICSHOPPING_MessageStack->exists('account_notification') ) {
    echo $CLICSHOPPING_MessageStack->get('account_notification');
  }
?>
<section class="account_notifications" id="account_notifications">
  <div class="contentContainer">
    <div class="contentText">
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_account_customers'); ?>
    </div>
  </div>
</section>