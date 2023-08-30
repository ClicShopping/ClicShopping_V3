<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;

?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="dfrom"
               class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_from'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('dfrom', null, 'aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_from') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_from') . '"', 'date'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <div class="form-group row">
        <label for="dto"
               class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_to'); ?></label>
        <div class="col-md-8">
          <?php echo HTML::inputField('dto', null, 'aria-describedby="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_to') . '" placeholder="' . CLICSHOPPING::getDef('modules_advanced_search_date_entry_date_to') . '"', 'date'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
