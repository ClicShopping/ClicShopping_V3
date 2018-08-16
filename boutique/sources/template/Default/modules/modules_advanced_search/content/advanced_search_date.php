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

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="dob" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_from'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('dfrom', null, 'id="dfrom" aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_from') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_from') . '"', 'date'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="dob" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_to'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('dto', null, 'id="dto" aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_to') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_to') . '"', 'date'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
