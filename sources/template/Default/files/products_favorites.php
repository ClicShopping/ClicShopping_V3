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
?>
<section class="favorites" id="favorites">
  <div class="contentContainer">
    <div class="contentText">
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_favorites'); ?>
    </div>
  </div>
</section>
