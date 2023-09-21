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
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-11">
      <div class="form-group row">
        <label for="inputSearch"
               class="col-2 col-form-label"><?php echo CLICSHOPPING::getDef('module_advanced_search_criteria_text'); ?></label>
        <div class="col-md-9">
          <?php echo HTML::inputField('keywords', null, 'required aria-required="true" id="inputSearch" aria-describedby="' . CLICSHOPPING::getDef('module_advanced_search_criteria_text') . '" placeholder="' . CLICSHOPPING::getDef('module_advanced_search_criteria_text') . '"'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
