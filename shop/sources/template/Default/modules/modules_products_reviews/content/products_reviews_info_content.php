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
  <div class="row">
    <span class="col-md-10">
      <h3><?php echo $products_name ?></h3><br />
      <?php echo CLICSHOPPING::getDef('modules_products_reviews_info_content_text_review_date_added', ['date_added' => $date_added]); ?>
    </span>
    <span class="col-md-2 text-md-right"><?php echo $customer_rating; ?></span>
  </div>
  <h3><?php echo CLICSHOPPING::getDef('modules_products_reviews_info_content_text_review_text_review_by', ['customer_name' => $customer_name]); ?></h3>
  <div class="separator"></div>
  <div class="row">
    <span class="col-md-11"><?php echo $customer_text; ?></span>
    <span><?php echo $delete_reviews; ?></span>
  </div>
  <div class="separator"></div>
</div>
