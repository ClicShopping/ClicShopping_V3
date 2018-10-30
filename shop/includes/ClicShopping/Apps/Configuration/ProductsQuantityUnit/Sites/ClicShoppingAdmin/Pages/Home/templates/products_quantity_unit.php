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

use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_ProductsQuantityUnit = Registry::get('ProductsQuantityUnit');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Language = Registry::get('Language');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/products_unit.png', $CLICSHOPPING_ProductsQuantityUnit->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ProductsQuantityUnit->getDef('heading_title'); ?></span>
          <span class="col-md-9 text-md-right"><?php echo HTML::button($CLICSHOPPING_ProductsQuantityUnit->getDef('button_insert'), null, $CLICSHOPPING_ProductsQuantityUnit->link('Insert'), 'success', null, 'xs'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th><?php echo $CLICSHOPPING_ProductsQuantityUnit->getDef('table_heading_products_unit_quantity_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_ProductsQuantityUnit->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
<?php
      $QproductsQuantityUnit = $CLICSHOPPING_ProductsQuantityUnit->db->prepare('select  SQL_CALC_FOUND_ROWS  *
                                                                                from :table_products_quantity_unit
                                                                                where language_id = :language_id
                                                                                order by products_quantity_unit_id
                                                                                limit :page_set_offset,
                                                                                      :page_set_max_results
                                                                                ');

      $QproductsQuantityUnit->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QproductsQuantityUnit->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $QproductsQuantityUnit->execute();

      $listingTotalRow = $QproductsQuantityUnit->getPageSetTotalRows();

      if ($listingTotalRow > 0) {
      while ( $QproductsQuantityUnit->fetch()) {

      if ((!isset($_GET['oID']) || (isset($_GET['oID']) && ((int)$_GET['oID'] === $QproductsQuantityUnit->valueInt('products_quantity_unit_id')))) && !isset($oInfo) && (substr($action, 0, 3) != 'new')) {
        $oInfo = new ObjectInfo($QproductsQuantityUnit->toArray());
      }

      if (DEFAULT_PRODUCTS_QUANTITY_UNIT_STATUS_ID == $QproductsQuantityUnit->valueInt('products_quantity_unit_id')) {
        echo '                <th scope="row"><strong>' . $QproductsQuantityUnit->value('products_quantity_unit_title') . ' (' . $CLICSHOPPING_ProductsQuantityUnit->getDef('text_default') . ')</strong></th>' . "\n";
      } else {
        echo '                <th scope="row">' . $QproductsQuantityUnit->value('products_quantity_unit_title') . '</th>' . "\n";
      }
?>
        <td class="text-md-right">
<?php
      if ($QproductsQuantityUnit->valueInt('products_quantity_unit_id') > 1) {
        echo '<a href="' . $CLICSHOPPING_ProductsQuantityUnit->link('Delete&page=' . $_GET['page'] . '&oID=' . $QproductsQuantityUnit->valueInt('products_quantity_unit_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_ProductsQuantityUnit->getDef('icon_delete')) . '</a>';
      }
      echo '&nbsp;';
      echo '<a href="' . $CLICSHOPPING_ProductsQuantityUnit->link('Edit&page=' . $_GET['page'] . '&oID=' . $QproductsQuantityUnit->valueInt('products_quantity_unit_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_ProductsQuantityUnit->getDef('icon_edit')) . '</a>' ;
?>
            </td>
          </tbody>
        </tr>
<?php
    } //enwhile
  } // end $listingTotalRow
?>
      </table>
    </td>
  </table>
<?php
  if ($listingTotalRow > 0) {
?>
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $QproductsQuantityUnit->getPageSetLabel($CLICSHOPPING_ProductsQuantityUnit->getDef('text_display_number_of_link')); ?></div>
          <div class="float-md-right text-md-right"><?php echo $QproductsQuantityUnit->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
<?php
  }
?>
</div>