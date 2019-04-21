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
<section class="boxe_currencies" id="boxe_currencies">
  <div class="card boxeContainerCurrencies">
    <div class="card-img-top boxeBannerContentsCurrencies"><?php echo $currencies_banner; ?></div>
    <div class="card-header boxeHeadingCurrencies">
      <span class="card-title boxeTitleCurrencies"><?php echo CLICSHOPPING::getDef('module_boxes_currencies_box_title'); ?></span>
    </div>
    <div class="card-block boxeContentArroundCurrencies">
      <div class="separator"></div>
      <div class="card-text boxeContentsCurrencies"><?php echo $output; ?></div>
    </div>
    <div class="card-footer boxeBottomCurrencies"></div>
  </div>
</section>
