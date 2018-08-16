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
?>
<section class="index" id="index">
  <div class="contentContainer">
    <div class="contentText">
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_sitemap'); ?>
    </div>
  </div>
</section>
