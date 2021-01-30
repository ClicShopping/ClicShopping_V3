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

  if ($CLICSHOPPING_MessageStack->exists('rewiews_write')) {
    echo $CLICSHOPPING_MessageStack->get('rewiews_write');
  }

  $message_alert = CLICSHOPPING::getDef('text_alert_products_reviews');
  $min_caracters_to_write = (int)REVIEW_TEXT_MIN_LENGTH;

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  echo HTML::form('product_reviews_write', CLICSHOPPING::link(null, 'Products&ReviewsWrite&Process&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'post', 'id="productReviewsWrite"', ['tokenize' => true, 'action' => 'process']);
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