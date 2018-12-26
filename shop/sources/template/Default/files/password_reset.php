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

  use ClicShopping\OM\CLICSHOPPING;

  if ( $CLICSHOPPING_MessageStack->exists('password_forgotten') ) {
    echo $CLICSHOPPING_MessageStack->get('password_forgotten');
  }

  if ( $CLICSHOPPING_MessageStack->exists('password_reset') ) {
    echo $CLICSHOPPING_MessageStack->get('password_reset');
  }

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="password_reset" id="password_reset">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-header modulesAccountCustomersPasswordResetPageHeader"><h1><?php echo CLICSHOPPING::getDef('heading_title'); ?></h1></div>
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_login'); ?>
    </div>
  </div>
</section>
<?php
  require($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));

