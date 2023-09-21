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
<section class="boxe_information" id="boxe_information">
  <div class="separator"></div>
  <div class="boxeBannerContentsPageManager"><?php echo $page_manager_banner; ?></div>
  <div class="card boxeContainerPageManager">
    <div class="card-header boxeHeadingPageManager">
      <span
        class="card-title boxeTitlePageManager"><?php echo CLICSHOPPING::getDef('module_boxes_page_manager_box_title'); ?></span>
    </div>
    <div class="card-body boxeContentArroundPageManager">
      <div class="card-text boxeContentsPageManager">
        <ul class="boxeListManagerPageManager">
          <li class="list-inline-item boxeListManagerPageManager"><?php echo $link; ?></li>
        </ul>
      </div>
    </div>
    <div class="card-footer boxeBottomPageManager"></div>
  </div>

