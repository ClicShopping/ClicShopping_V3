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
<section class="boxe_manufacturers" id="boxe_manufacturers">
  <div class="separator"></div>
  <div class="boxeBannerContentsManufacturer"><?php echo $manufacturer_banner; ?></div>
  <div class="card boxeContainerManufacturer">
    <div class="card-header boxeHeadingManufacturer">
      <span class="card-title boxeTitleManufacturer"><?php echo CLICSHOPPING::getDef('module_boxes_manufacturers_title'); ?></span>
    </div>
    <div class="card-block boxeContentArroundManufacturer">
      <div class="separator"></div>
      <div class="card-text boxeContentsManufacturer"><?php echo $output; ?></div>
    </div>
    <div class="card-footer boxeBottomManufacturer"></div>
  </div>
  <div class="separator"></div>
</section>