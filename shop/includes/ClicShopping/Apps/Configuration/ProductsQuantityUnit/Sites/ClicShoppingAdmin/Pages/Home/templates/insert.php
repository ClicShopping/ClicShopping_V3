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

  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_ProductsQuantityUnit = Registry::get('ProductsQuantityUnit');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/products_unit.png', $CLICSHOPPING_ProductsQuantityUnit->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ProductsQuantityUnit->getDef('heading_title'); ?></span>
          <span class="col-md-9 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_ProductsQuantityUnit->getDef('button_cancel'), null, $CLICSHOPPING_ProductsQuantityUnit->link('ProductsQuantityUnit'), 'warning')  . ' ';
  echo HTML::form('status_products_quantity_unit', $CLICSHOPPING_ProductsQuantityUnit->link('ProductsQuantityUnit&Insert&page=' . $page));
  echo HTML::button($CLICSHOPPING_ProductsQuantityUnit->getDef('button_insert'), null, null, 'success')
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_ProductsQuantityUnit->getDef('text_info_heading_products_unit_quantity_status'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12"><?php echo  $CLICSHOPPING_ProductsQuantityUnit->getDef('text_info_products_quantity_unit_intro'); ?><br/><br/></div>
      <div class="separator"></div>
      <div class="col-md-12">
<?php
  $products_quantity_unit_inputs_string = '';

  $languages = $CLICSHOPPING_Language->getLanguages();
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
        <div class="row">
          <div class="col-md-5">
            <div class="form-group row">
              <label for="code" class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
              <div class="col-md-5">
                <?php echo HTML::inputField('products_quantity_unit_title[' . $languages[$i]['id'] . ']', null, 'class="form-control" required aria-required="true"'); ?>
              </div>
            </div>
          </div>
        </div>
<?php
  }
?>
      </div>

      <div class="separator"></div>
      <div class="col-md-12">
        <span class="col-md-3"></span>
        <span class="col-md-3"><br /><?php echo HTML::checkboxField('default') . ' ' . $CLICSHOPPING_ProductsQuantityUnit->getDef('text_set_default'); ?></span>
      </div>
  </div>
  </form>
</div>