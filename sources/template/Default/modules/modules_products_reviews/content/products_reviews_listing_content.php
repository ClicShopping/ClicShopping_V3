<div class="<?php echo $text_position; ?> col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="row">
    <span class="col-md-8"><?php echo $date_reviews . '<br />' . $customer_review; ?></span>
    <span class="col-md-4 text-end productsReviewsListingContentRating" itemprop="ratingValue"><?php echo $review_star; ?></span>
  </div>
  <div class="separator"></div>
  <div class="row">
    <span class="col-md-10"><?php echo $review_text; ?></span>
    <?php
      if ($delete_comment == 'True') {
    ?>
    <span class="col-md-2 text-end"><?php echo $delete_reviews ?></span>
    <?php
      }
    ?>
  </div>
  <div class="separator"></div>
  <div class="hr"></div>
  <div class="separator"></div>
</div>