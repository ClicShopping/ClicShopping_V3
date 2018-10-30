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
    <div class="col-md-11">
      <div class="form-group row">
        <label for="dob" class="col-2 col-form-label"><?php echo CLICSHOPPING::getDef('module_advanced_search_criteria_text'); ?></label>
        <div class="col-md-9">
          <?php echo HTML::inputField('keywords', null, 'required aria-required="true" id="inputSearch" aria-describedby="' . CLICSHOPPING::getDef('module_advanced_search_criteria_text') . '" placeholder="' . CLICSHOPPING::getDef('module_advanced_search_criteria_text') . '"'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
