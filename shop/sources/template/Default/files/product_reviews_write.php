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
// Do not touch the script below
?>
<script src="<?php echo CLICSHOPPING::link($CLICSHOPPING_Template->getTemplateDefaultJavaScript('clicshopping/review_write.js.php')); ?>"></script>

<?php
  if ( $CLICSHOPPING_MessageStack->exists('review_write') ) {
    echo $CLICSHOPPING_MessageStack->get('review_write');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  echo HTML::form('product_reviews_write', CLICSHOPPING::link(null, 'Products&ReviewsWrite&Process&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'post', 'id="productReviewsWrite" onsubmit="return checkForm();"', ['tokenize' => true, 'action' => 'process']);
?>
<section class="product_reviews_write" id="product_reviews_write">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-header"><h1><?php echo CLICSHOPPING::getDef('heading_title_reviews'); ?></h1></div>
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_reviews'); ?>
    </div>
  </div>
</section>
</form>
