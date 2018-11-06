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

use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
?>
<section class="boxe_search" id="boxe_search">
  <div class="card boxeContainerSearch">
    <div class="card-img-top boxeBannerContentsSearch"><?php echo $search_banner; ?></div>
    <div class="card-header boxeHeadingSearch">
      <span class="card-title boxeTitleSearch"><?php echo CLICSHOPPING::getDef('module_boxes_search_box_title'); ?></span>
    </div>
    <div class="card-block boxeContentArroundSearch">
      <div class="card-text text-md-center boxeContentsSearch">
<?php
  echo $output;
  echo '<div class="boxeContentLinkAdvancedSearch">' . HTML::link(CLICSHOPPING::link(null, 'Search&AdvancedSearch'), CLICSHOPPING::getDef('module_boxes_search_box_advanced_search')) . '</div>';
?>
      </div>
    </div>
    <div class="card-footer boxeBottomSearch"></div>
  </div>
</section>