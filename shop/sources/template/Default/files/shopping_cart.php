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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="cart" id="cart">
  <div class="contentContainer">
    <div class="contentText">
      <div class="page-header">
        <h1><?php echo CLICSHOPPING::getDef('heading_title'); ?></h1>
      </div>
      <div class="d-flex flex-wrap">
        <div class="col-md-12">

<?php
  if ($CLICSHOPPING_ShoppingCart->getCountContents() > 0) {
    echo $CLICSHOPPING_Template->getBlocks('modules_shopping_cart');
  } else {
?>
          <div class="clearfix"></div>
          <div class="separator"></div>
          <div class="col-md-12">
            <div class="alert alert-warning text-md-center" role="alert"><h3><?php echo CLICSHOPPING::getDef('text_cart_empty'); ?></h3></div>
            <div class="buttonSet float-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), '', CLICSHOPPING::link(), 'success'); ?></div>
          </div>
<?php
  }
?>
        </div>
      </div>
    </div>
  </div>
</section>