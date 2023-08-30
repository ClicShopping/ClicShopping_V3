<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Template = Registry::get('Template');
$CLICSHOPPING_Customer = Registry::get('Customer');

if (!$CLICSHOPPING_Customer->isLoggedOn()) {
  CLICSHOPPING::redirect(null, 'Account&LogIn');
}

if ($CLICSHOPPING_MessageStack->exists('rewiews_write')) {
  echo $CLICSHOPPING_MessageStack->get('rewiews_write');
}
?>
<?php require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb')); ?>
<section class="product_reviews_write_success" id="product_reviews_write_success">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-title"><h1><?php echo CLICSHOPPING::getDef('heading_title_reviews_success'); ?></h1></div>
      <?php echo CLICSHOPPING::getDef('text_product_reviews_write_success', ['store_owner' => STORE_OWNER]); ?>
    </div>
    <div class="separator"></div>
    <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_reviews'); ?>
  </div>
</section>
