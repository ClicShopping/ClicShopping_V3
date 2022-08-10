<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
?>
<section class="boxe_languages" id="boxe_languages">
  <div class="separator"></div>
  <div class="boxeBannerContentsLanguages"><?php echo $languages_banner; ?></div>
  <div class="card boxeContainerLanguages">
    <div class="card-header boxeHeadingLanguages">
      <span class="card-title boxeTitleLanguages"><?php echo CLICSHOPPING::getDef('module_boxes_languages_box_title'); ?></span>
    </div>
    <div class="card-body boxeContentArroundLanguages">
      <div class="separator"></div>
      <div class="card-text boxeContentsLanguages"><?php echo $languages_string; ?></div>
    </div>
    <div class="card-footer boxeBottomLanguages"></div>
  </div>
</section>