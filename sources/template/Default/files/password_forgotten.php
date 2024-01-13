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

if ($CLICSHOPPING_MessageStack->exists('main')) {
  echo $CLICSHOPPING_MessageStack->get('main');
}

require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="password_forgotten" id="password_forgotten">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-title modulesAccountCustomersPasswordForgottenPageHeader">
        <h1><?php echo CLICSHOPPING::getDef('heading_title'); ?></h1></div>

      <?php echo $CLICSHOPPING_Template->getBlocks('modules_login'); ?>
      <div class="mt-1"></div>
    </div>
  </div>
</section>
