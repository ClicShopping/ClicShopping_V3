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
<div class="col-12 col-sm-<?php echo $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?> ModulesProductsInfoBoostrapColumn5">
  <div class="separator"></div>
  <div class="card">
    <div class="card-height">
        <div class="separator"></div>
          <div class="card-img-top ModulesProductsInfoBoostrapColumn5Image">
          <?php echo $products_image . $ticker; ?>
        </div>
        <div class="card-body">
          <div class="ModulesProductsInfoBoostrapColumn5Title"><h3><?php echo $products_name; ?></h3></div>
          <div class="separator"></div>
          <div class="separator"></div>
<?php
  if (!empty($products_short_description)) {
?>
      <div class="ModulesProductsInfoBoostrapColumn5ShortDescription"><h3><?php echo $products_short_description; ?></h3></div>
<?php
  }
?>
        </div>
       <div>
<?php
   if (!empty($products_stock)) {
?>
      <div class="ModulesProductsInfoBoostrapColumn5StockImage"><?php echo $products_stock; ?></div>
<?php
  }
  if (!empty($min_order_quantity_products_display)) {
?>
      <div class="ModulesProductsInfoBoostrapColumn5QuantityMinOrder"><?php echo  $min_order_quantity_products_display; ?></div>
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
            <div class="ModulesProductsInfoBoostrapColumn5TextPrice" ><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $product_price; ?></div>
      </div>
      <?php echo $form; ?>
      <div class="form-group form-group-center">
            <span class="ModulesProductsInfoBoostrapColumn5QuantityMinOrder"><?php echo $input_quantity; ?>&nbsp; </span>
            <span class="ModulesProductsInfoBoostrapColumn5ViewDetails"><?php echo $button_small_view_details; ?>&nbsp; </span>
            <span class="ModulesProductsInfoBoostrapColumn5SubmitButton"><label for="ModulesProductsInfoBoostrapColumn5SubmitButton"><?php echo $submit_button; ?></label></span>
      </div>
      <?php echo $endform; ?>
    </div>
  </div>
</div>
<?php echo $jsonLtd; ?>
