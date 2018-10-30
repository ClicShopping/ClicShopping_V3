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

use ClicShopping\OM\CLICSHOPPING;

  echo '
    function checkForm() {
      var error = 0;
      var error_message = "' . CLICSHOPPING::getDef('js_error') . '";

      var review = document.product_reviews_write.review.value;

      if (review.length ' . (int)REVIEW_TEXT_MIN_LENGTH . ') {
        error_message = error_message + "' .  CLICSHOPPING::getDef('js_review_text') . '";
        error = 1;
      }

      if ((document.product_reviews_write.rating[0].checked) || (document.product_reviews_write.rating[1].checked) || (document.product_reviews_write.rating[2].checked) || (document.product_reviews_write.rating[3].checked) || (document.product_reviews_write.rating[4].checked)) {
      } else {
        error_message = error_message + "'.  CLICSHOPPING::getDef('js_review_rating') . '";
        error = 1;
      }

      if (error == 1) {
        alert(error_message);
        return false;
      } else {
        return true;
      }
    }
  ';