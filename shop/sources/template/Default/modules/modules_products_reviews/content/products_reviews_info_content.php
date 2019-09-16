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
<div class="<?php echo $text_position; ?> col-md-<?php echo $content_width; ?>">
  <div class="row">
    <div class="col-md-12">
       <span class="float-md-left col-md-7 productsReviewInfoContentCustomerProductsName"><h3><?php echo $products_name ?></h3></span>
       <span class="col-md-4 text-md-right productsReviewInfoContentCustomerRating">
         <?php echo $customer_rating; ?>
       </span>
      <span class="col-md-1 text-md-right productsReviewInfoContentCustomerDeleteReviews">
         <?php
           if ($delete_comment == 'True') {
             ?>
             <span><?php echo $delete_reviews; ?></span>
             <?php
           }
         ?>
       </span>
    </div>
    <div class="separator"></div>
    <div class="col-md-12 productsReviewInfoContentDateAdded">
      <?php echo CLICSHOPPING::getDef('modules_products_reviews_info_content_text_review_date_added', ['date_added' => $date_added]); ?>
    </div>
  </div>
  <div class="separator"></div>
  <div class="productsReviewInfoContentCustomerReviewBy">
    <h3><?php echo CLICSHOPPING::getDef('modules_products_reviews_info_content_text_review_text_review_by', ['customer_name' => $customer_name]); ?></h3>
  </div>
  <div class="separator"></div>
  <div class="row">
    <span class="col-md-11" class="productsReviewInfoContentCustomerText"><?php echo $customer_text; ?></span>
  </div>
  <div class="separator"></div>
</div>
