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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Customer = Registry::get('Customer');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

  if (!$CLICSHOPPING_Customer->isLoggedOn()) {
    CLICSHOPPING::redirect(null, 'Account&LogIn');
  }
?>

<script>
    function checkForm() {
        var error = 0;
        var error_message = "Errors have occured during the process of your form";

        var review = document.product_reviews_write.review.value;

        if (review.length = <?php echo (int)REVIEW_TEXT_MIN_LENGTH; ?>) {
            error_message = error_message + "* Le commentaire que vous avez rentré doit avoir au moins' <?php echo (int)REVIEW_TEXT_MIN_LENGTH; ?> 'caracters";
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
</script>
<?php
  if ( $CLICSHOPPING_MessageStack->exists('main') ) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  echo HTML::form('product_reviews_write', CLICSHOPPING::link(null, 'Products&ReviewsWrite&Process&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'post', 'id="productReviewsWrite" onsubmit="return checkForm();"', ['tokenize' => true, 'action' => 'process']);
?>
<section class="product_reviews_write" id="product_reviews_write">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-title"><h1><?php echo CLICSHOPPING::getDef('heading_title_reviews'); ?></h1></div>
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_reviews'); ?>
    </div>
  </div>
</section>
</form>