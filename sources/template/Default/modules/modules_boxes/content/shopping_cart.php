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
  use ClicShopping\OM\HTML;
?>
<section class="boxe_shopping_cart" id="boxe_shopping_cart">
  <div class="separator"></div>
  <div class="ClicShoppingboxContentShoppingCartBanner"><?php echo $shopping_cart_banner; ?></div>
  <div class="card boxeContainerShoppingCart">
    <div class="card-header ClicShoppingboxHeadingShoppingCart">
      <span class="card-title boxeTitleShoppingCart"><?php echo HTML::link(CLICSHOPPING::link(null,'Cart'), CLICSHOPPING::getDef('module_boxes_shopping_cart_box_title')); ?></span>
    </div>
    <div class="card-block boxeContentArroundShoppingCart">
      <div class="separator"></div>
      <?php echo $cart_contents_string; ?>
    </div>
    <div class="card-footer boxeBottomContentsShoppingCart"></div>
  </div>
</section>