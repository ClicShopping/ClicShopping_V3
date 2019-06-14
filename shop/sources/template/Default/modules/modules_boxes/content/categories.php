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

  $CLICSHOPPING_CategoryTree->setCategoryPath($cPath, '<span class="boxeCategoriesNavigation">', '</span>');
  $CLICSHOPPING_CategoryTree->setSpacerString('&nbsp;&nbsp;', 1);
  $CLICSHOPPING_CategoryTree->setParentGroupString('<ul class="boxeCategoriesNavigation">', '</ul>', true);
  $CLICSHOPPING_CategoryTree->setChildString('<li class="boxeCategoriesNavigation">', '</li>');
?>
<section class="boxe_categories" id="boxe_categories">
  <div class="card boxeContainerCategories">
    <div class="card-img-top boxeBannerContentsCategories"><?php echo $categories_banner; ?></div>
    <div class="card-header boxeHeadingCategories">
      <span class="card-title boxeTitleCategories"><?php echo CLICSHOPPING::getDef('module_boxes_categories_box_title'); ?></span>
    </div>
    <div class="card-block boxeContentArroundCategories">
      <div class="card-text boxeContentsCategories"><?php echo $CLICSHOPPING_CategoryTree->getTree(); ?></div>
    </div>
  </div>
</section>