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
<section class="boxe_information" id="boxe_information">
  <div class="card boxeContainerPageManager">
    <div class="card-img-top boxeBannerContentsPageManager"><?php echo $page_manager_banner; ?></div>
    <div class="card-header boxeHeadingPageManager">
      <span class="card-title boxeTitlePageManager"><?php echo CLICSHOPPING::getDef('module_boxes_page_manager_box_title'); ?></span>
    </div>
    <div class="card-block boxeContentArroundPageManager">
      <div class="card-text boxeContentsPageManager">
        <ul class="list-inline">
          <li class="list-inline-item boxeListManagerPageManager"><?php echo $link; ?></li>
        </ul>
      </div>
    </div>
    <div class="card-footer boxeBottomPageManager"></div>
  </div>

