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

?>

<div class="<?php echo $text_position; ?> col-md-<?php echo $content_width; ?>">
  <div class="mt-1"></div>
  <div class="row">
    <div class="col-md-12">
      <div>
        <label for="inputName"
               class="col-4 col-form-label"><?php echo CLICSHOPPING::getDef('modules_products_reviews_write_comment_sub_title_from'); ?></label>
        <div class="col-md-8">
          <?php echo $customer_name; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div>
        <label for="inputReview"
               class="col-3 col-form-label"><?php echo CLICSHOPPING::getDef('modules_products_reviews_write_comment_sub_title_from_sub_title_review'); ?></label>
        <div class="col-md-10">
          <div class="form-group">
            <?php echo $comment; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-info" role="alert" role="alert">
        <?php
        echo CLICSHOPPING::getDef('modules_products_reviews_write_min_caracters') . ' ' . $min_caracters_to_write . '<br />';
        echo CLICSHOPPING::getDef('modules_products_reviews_write_comment_sub_title_from_sub_title_review_text_no_html');
        ?>
        <?php
        if ($customer_group_id > 0) {
          ?>
          <div class="mt-1"></div>
          <div><?php echo CLICSHOPPING::getDef('modules_products_reviews_write_comment_customer_group'); ?></div>
          <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>