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

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="<?php echo $text_position; ?> col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
    <div class="page-header">
      <h4><?php echo CLICSHOPPING::getDef('text_product_options'); ?></h4>
    </div>
    <div>
      <div class="col-md-6">
<?php
  foreach ( $products_options_name_array as $key => $value )  {
?>
        <div class="form-group">
          <label class="col-md-3"><?php echo $key . ':'; ?></label>
          <div class="col-md-9">
            <?php echo HTML::selectMenu('id[' . $value . ']', $products_options_array[$value], $selected_attribute[$value]); ?>
          </div>
        </div>
<?php
  }
?>
      </div>
    </div>
</div>