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
?>
<div class="<?php echo $text_position; ?> col-md-<?php echo $content_width; ?>">
  <div class="card ModulesProductsInfoBackground">
    <div class="card-block">
      <div class="card-text">
        <div class="ModulesProductsInfoPrice">
          <?php echo CLICSHOPPING::getDef('text_price_info'); ?>
            <span itemprop="offers" itemscope itemtype="https://schema.org/Offer">
              <?php echo $product_price; ?>
            </span>
          <div class="ModulesProductsInfokiloPrice">
            <span class="kiloPrice"><?php echo $product_price_kilo; ?></span>
          </div>
        </div>
        <div class="separator"></div>
        <div class="ModulesProductsInfoMinOrderQuandityProductsDisplay">
          <span class="ModulesProductsInfoMinOrderQuandityProductsDisplay"><h3><?php echo $min_order_quantity_products_display; ?></h3></span>
        </div>
        <div class="separator"></div>
        <div class="text-md-right ModulesProductsInfoQuantityMinOrderProductInfo">
          <label for="Quantity Product" class="sr-only">Quantity Product</label>
          <span class="ModulesProductsInfoQuantityMinOrderProductInfo"><?php echo $input_quantity; ?></span>
        </div>
        <div class="separator"></div>
         <div class="modulesProductsInfoModulesProductsInfosubmitButton">
          <span class="modulesProductsInfoModulesProductsInfosubmitButton"><?php echo $submit_button; ?></span>
        </div>
      </div>
    </div>
  </div>
</div>