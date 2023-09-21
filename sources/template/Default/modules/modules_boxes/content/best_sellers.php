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

?>
<section class="boxe_best_sellers" id="boxe_best_sellers">
  <div class="separator"></div>
  <div class="boxeBannerContentsBestSellers"><?php echo $best_sellers_banner; ?></div>
  <div class="card boxeContainerBestSellers" itemscope itemtype="https://schema.org/ItemList">
    <meta itemprop="itemListOrder" content="https://schema.org/ItemListOrderDescending"/>
    <div class="card-header boxeHeadingBestSellers"
         itemprop="name"><?php echo CLICSHOPPING::getDef('module_boxes_best_sellers_box_title'); ?></div>
    <div class="card-body boxeContentArroundBestSellers">
      <div class="separator"></div>
      <?php echo $bestsellers_list; ?>
    </div>
  </div>
</section>
