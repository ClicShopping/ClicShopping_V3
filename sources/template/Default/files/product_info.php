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
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

// ----------------------------------------------------------------//
//                      file not found                             //
// ----------------------------------------------------------------//

  if ($CLICSHOPPING_ProductsCommon->getProductsCount() < 1 || (\is_null($CLICSHOPPING_ProductsCommon->getID())) || $CLICSHOPPING_ProductsCommon->getID() === false ) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
?>
 <section class="product" id="product">
  <div class="contentContainer">
    <div class="contentText">
      <div class="separator"></div>
      <div class="separator"></div>
      <div class="alert alert-warning text-center" role="alert">
         <h3><?php echo CLICSHOPPING::getDef('text_product_not_found'); ?></h3>
      </div>
      <div class="separator"></div>
      <div class="control-group">
        <div>
          <div class="buttonSet">
            <span class="float-end"><label for="buttonContinue"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(),'success'); ?></label></span>
          </div>
        </div>
      </div>
    </div>
  </div>
 </section>
<?php
  } elseif ($CLICSHOPPING_ProductsCommon->getProductsGroupView() == 1 ||  $CLICSHOPPING_ProductsCommon->getProductsView() == 1) {
// ----------------------------------------------------------------
// ---- Display products with autorization  ----
// ------------------------------------------------------------
    require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
    $CLICSHOPPING_ProductsCommon->countUpdateProductsView();
?>
<section class="product" id="product">
  <div class="contentContainer">
    <div class="contentText">
      <div class="productsInfoContent">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_info'); ?>
      </div>
    </div>
  </div>
</section>
<?php
  }
