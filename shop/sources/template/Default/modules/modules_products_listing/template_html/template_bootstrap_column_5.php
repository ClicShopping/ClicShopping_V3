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
<div class="col-md-<?php echo $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div style="padding-top:1rem;"></div>
  <div class="card-deck-wrapper" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/Product">
    <div class="card-deck">
      <div class="card">
        <div class="card-block">
          <div class="separator"></div>
          <div class="card-img-top ModulesProductsListingBoostrapColumn5Image">
            <?php echo $products_image . $ticker; ?>
          </div>
          <div class="ModulesProductsListingBoostrapColumn5Title"><h3><?php echo $manufacturer_name; ?></h3></div>
          <div class="separator"></div>
          <div class="separator"></div>
<?php
  if (!empty($products_short_description)) {
?>
          <div class="ModulesProductsListingBoostrapColumn5ShortDescription"><span itemprop="description"><h3><?php echo $products_short_description; ?></h3></span></div>
<?php
  }
?>
          <div>
<?php
   if (!empty($products_stock)) {
?>
            <div class="ModulesProductsListingBoostrapColumn5StockImage"><?php echo $products_stock; ?></div>
<?php
  }
  if (!empty($min_order_quantity_products_display)) {
?>
            <div class="ModulesProductsListingBoostrapColumn5QuantityMinOrder"><?php echo $min_order_quantity_products_display; ?></div>
<?php
  }
  if (!empty($products_flash_discount)) {
?>
            <div class="EndDateFlashDiscount"> <?php echo $products_flash_discount; ?></div>
<?php
  }
?>
          </div>
          <div>
            <div class="ModulesProductsListingBoostrapColumn5TextPrice" itemprop="offers" itemscope itemtype="https://schema.org/Offer"><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $product_price; ?></div>
          </div>
          <?php echo $form; ?>
          <div class="form-group form-group-center">
            <span class="ModulesProductsListingBoostrapColumn5QuantityMinOrder"><?php echo $input_quantity; ?>&nbsp; </span>
            <span class="ModulesProductsListingBoostrapColumn5ViewDetails"><?php echo $button_small_view_details; ?>&nbsp; </span>
            <span class="ModulesProductsListingBoostrapColumn5SubmitButton"><?php echo $submit_button; ?></span>
          </div>
          <?php echo $endform; ?>
        </div>
      </div>
     </div>
  </div>
</div>
