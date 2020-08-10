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
?>
<section class="boxe_information_customize" id="boxe_information_customize">
  <div class="separator"></div>
  <div class="boxeBannerContentsPageManagerCustomize"><?php echo $pm_customomize_banner; ?></div>
  <div class="card boxeContainerPageManagerCustomize">
    <div class="card-header boxeHeadingPageManagerCustomize">
      <span class="card-title boxeTitlePageManagerCustomize"><?php echo CLICSHOPPING::getDef('module_boxes_page_manager_customize_box_title'); ?></span>
    </div>
    <div class="card-block boxeContentArroundPageManagerCustomize">
      <div class="card-text boxeContentsPageManagerCustomize">
        <ul class="list-inline">
          <li class="list-inline-item boxeListManagerPageManagerCustomize"><?php echo $link; ?></li>
        </ul>
      </div>
    </div>
    <div class="card-footer boxeBottomPageManagerCustomize"></div>
  </div>
</section>