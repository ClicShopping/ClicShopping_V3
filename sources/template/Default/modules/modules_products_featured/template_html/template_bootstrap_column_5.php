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
<div class="col-12 col-md-<?php echo $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?> ModulesProductsFeaturedBoostrapColumn5">
  <div class="separator"></div>
  <div class="card">
    <div class="card-height">
          <div class="separator"></div>
          <div class="card-img-top ModulesProductsFeaturedBoostrapColumn5Image">
          <?php echo $products_image . $ticker; ?>
        </div>
        <div class="card-body">
          <div class="ModulesProductsNewBoostrapColumn5Title"><h3><?php echo $products_name; ?></h3></div>
          <div class="separator"></div>
          <div class="separator"></div>
<?php
  if (!empty($products_short_description)) {
?>
          <div class="ModulesProductsFeaturedBoostrapColumn5ShortDescription"><h3><?php echo $products_short_description; ?></h3></div>
<?php
  }
?>
        </div>
       <div>
<?php
   if (!empty($products_stock)) {
?>
          <div class="ModulesProductsFeaturedBoostrapColumn5StockImage"><?php echo $products_stock; ?></div>
<?php
  }
  if (!empty($min_order_quantity_products_display)) {
?>
          <div class="ModulesProductsFeaturedBoostrapColumn5QuantityMinOrder"><?php echo  $min_order_quantity_products_display; ?></div>
<?php
  }
  if (!empty($products_flash_discount)) {
?>
        <div class="separator"></div>
        <div class="EndDateFlashDiscount"> <?php echo $products_flash_discount; ?></div>
<?php
  }
?>
          </div>
          <div>
            <div class="ModulesProductsFeaturedBoostrapColumn5TextPrice" ><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $product_price; ?></div>
          </div>
          <?php echo $form; ?>
          <div class="form-group form-group-center">
            <span class="ModulesProductsFeaturedBoostrapColumn5QuantityMinOrder"><?php echo $input_quantity; ?>&nbsp; </span>
            <span class="ModulesProductsFeaturedBoostrapColumn5ViewDetails"><?php echo $button_small_view_details; ?>&nbsp; </span>
            <span class="ModulesProductsFeaturedBoostrapColumn5SubmitButton"><label for="ModulesProductsFeaturedBoostrapColumn5SubmitButton"><?php echo $submit_button; ?></label></span>
          </div>
          <?php echo $endform; ?>
    </div>
  </div>
</div>
<?php echo $jsonLtd; ?>