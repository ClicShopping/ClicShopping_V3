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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<div class="clearfix"></div>
<div class="separator"></div>
<section class="product_reviews_info" id="product_reviews_info">
    <div class="contentContainer">
      <div class="contentText">
        <div class="page-header"><h1><?php echo CLICSHOPPING::getDef('heading_title_review_info'); ?></h1></div>
      </div>
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_reviews'); ?>
    </div>
</section>

