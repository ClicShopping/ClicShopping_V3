<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;

?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="mt-1"></div>
  <div>
    <div class="col-md-7">
      <div class="form-group row">
        <label for="PriceFrom"
               class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_from'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('pfrom', null, 'style="width: 150px;" id="PriceFrom" aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_from') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_from') . '"'); ?>
        </div>
      </div>
    </div>
  </div>

  <div>
    <div class="col-md-7">
      <div class="form-group row">
        <label for="PriceTo"
               class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_to'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('pto', null, 'style="width: 150px;" id="PriceTo" aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_to') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_price_entry_price_to') . '"'); ?>
        </div>
      </div>
    </div>
  </div>

</div>