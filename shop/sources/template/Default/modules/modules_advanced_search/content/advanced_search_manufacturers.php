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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="entryManufacturers" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_manufacturers_entry_manufacturers'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::selectMenu('manufacturers_id', $CLICSHOPPING_ProductsCommon->getManufacturersDropDown(array(array('id' => '', 'text' => CLICSHOPPING::getDef('modules_advanced_search_manufacturers_text_all_manufacturers')))), NULL, 'id="entryManufacturers"'); ?>
        </div>
      </div>
    </div>
  </div>
</div>