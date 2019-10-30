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

  echo '
    function checkForm() {
      var error = 0;
      var error_message = "Errors have occured during the process of your form";

      var review = document.product_reviews_write.review.value;

      if (review.length = ' . (int)REVIEW_TEXT_MIN_LENGTH . ') {
        error_message = error_message + "* Le commentaire que vous avez rentré doit avoir au moins' . (int)REVIEW_TEXT_MIN_LENGTH . 'caracters";
        error = 1;
      }

      if ((document.product_reviews_write.rating[0].checked) || (document.product_reviews_write.rating[1].checked) || (document.product_reviews_write.rating[2].checked) || (document.product_reviews_write.rating[3].checked) || (document.product_reviews_write.rating[4].checked)) {
      } else {
        error_message = error_message + " * You must rate the product for your review";
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