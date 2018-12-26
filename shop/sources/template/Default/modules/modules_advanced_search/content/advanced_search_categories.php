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
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="InputCategory" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_category_entry_categories'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::selectMenu('categories_id', $CLICSHOPPING_Category->getCategories(array(array('id' => '', 'text' => CLICSHOPPING::getDef('modules_advanced_search_category_entry_categories')))), NULL, 'id="InputCategory"'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="InputCategory" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_category_entry_include_subcategories'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::checkboxField('inc_subcat', 1, true, 'id="inc_subcat" aria-label="' . CLICSHOPPING::getDef('modules_advanced_search_category_entry_include_subcategories') . '"'); ?>
        </div>
      </div>
    </div>
  </div>
</div>