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
  <div class="col-md-<?php echo $content_width; ?>" id="buttonProcess1">
    <div class="separator"></div>
    <div class="control-group">
      <div class="buttonSet">
        <div class="text-end">
          <?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, null, 'primary', null, null, null, '"submit"'); ?>
        </div>
      </div>
    </div>
  </div>
<?php echo $endform; ?>