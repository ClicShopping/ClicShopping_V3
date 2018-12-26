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

use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="col-md-4"><?php echo CLICSHOPPING::getDef('modules_products_reviews_write_rating_text_sub_title_rating'); ?></div>
  <div class="col-md-8">
    <?php echo $rating; ?>
  </div>
</div>