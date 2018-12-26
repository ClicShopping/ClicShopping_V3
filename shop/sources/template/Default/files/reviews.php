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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Reviews = Registry::get('Reviews');

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="reviews" id="reviews">
  <div class="contentContainer">
    <div class="contentText">
<?php
  if ($CLICSHOPPING_Reviews->getTotalReviews() == 0) {
?>
      <div class="separator"></div>
      <div class="alert alert-info"><?php echo CLICSHOPPING::getDef('text_no_reviews'); ?></div>
<?php
  } else {
    echo $CLICSHOPPING_Template->getBlocks('modules_products_reviews');
  }
?>
    </div>
  </div>
</section>
