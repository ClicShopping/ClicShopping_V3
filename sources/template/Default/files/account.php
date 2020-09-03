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

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  if ($CLICSHOPPING_MessageStack->exists('main')) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }
?>
<section class="account" id="account">
  <div class="hr"></div>
  <div class="contentContainer">
    <div class="contentText">
      <div class="d-flex flex-wrap">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_account_customers'); ?>
      </div>
    </div>
  </div>
</section>