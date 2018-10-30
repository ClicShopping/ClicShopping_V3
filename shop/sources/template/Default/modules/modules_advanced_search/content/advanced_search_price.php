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
  <div>
    <div class="col-md-7">
      <div class="form-group row">
        <label for="dob" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_from'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('pfrom', null, 'style="width: 150px;" id="PriceFrom" aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_from') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_from') . '"'); ?>
        </div>
      </div>
    </div>
  </div>

  <div>
    <div class="col-md-7">
      <div class="form-group row">
        <label for="dob" class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_from'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('pto', null, 'style="width: 150px;" id="PriceTo" aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_to') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_to') . '"'); ?>
        </div>
      </div>
    </div>
  </div>

</div>