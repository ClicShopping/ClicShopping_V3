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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Configuration\ProductsLength\Classes\ClicShoppingAdmin\ProductsLengthAdmin;

  $CLICSHOPPING_ProductsLength = Registry::get('ProductsLength');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

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
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/products_length.png', $CLICSHOPPING_ProductsLength->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ProductsLength->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_ProductsLength->getDef('button_insert_products_length'), null, $CLICSHOPPING_ProductsLength->link('ProductsLengthInsert&page=' . $_GET['page']), 'primary') . ' ';
  echo HTML::button($CLICSHOPPING_ProductsLength->getDef('button_insert_class'), null, $CLICSHOPPING_ProductsLength->link('ClassInsert&page=' . $_GET['page']), 'success');
?>
          </span>
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
          <td><?php echo $CLICSHOPPING_ProductsLength->getDef('table_heading_products_length_class_id'); ?></td>
          <td><?php echo $CLICSHOPPING_ProductsLength->getDef('table_heading_products_length_class_symbol'); ?></td>
          <td><?php echo $CLICSHOPPING_ProductsLength->getDef('table_heading_products_length_class_type'); ?></td>
          <td><?php echo $CLICSHOPPING_ProductsLength->getDef('table_heading_products_length_class_to_id'); ?></td>
          <td><?php echo $CLICSHOPPING_ProductsLength->getDef('table_heading_products_length_class_rule'); ?></td>
          <td class="text-md-right"><?php echo $CLICSHOPPING_ProductsLength->getDef('table_heading_action'); ?>&nbsp;</td>
        </tr>
        </thead>
        <tbody>
<?php

  $Qproducts_length = $CLICSHOPPING_ProductsLength->db->prepare('select SQL_CALC_FOUND_ROWS  wc.products_length_class_id,
                                                                                             wc.products_length_class_key,
                                                                                             wc.language_id,
                                                                                             wc.products_length_class_title,
                                                                                             tc.products_length_class_from_id,
                                                                                             tc.products_length_class_to_id,
                                                                                             tc.products_length_class_rule
                                                              from :table_products_length_classes wc,
                                                                   :table_products_length_classes_rules tc 
                                                              where wc.products_length_class_id = tc.products_length_class_from_id
                                                              and wc.language_id = :language_id
                                                              limit :page_set_offset,
                                                                    :page_set_max_results
                                                              ');
  $Qproducts_length->bindInt(':language_id', $CLICSHOPPING_Language->getID());
  $Qproducts_length->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
  $Qproducts_length->execute();

  $listingTotalRow = $Qproducts_length->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

    while ($Qproducts_length->fetch()) {
      if ((!isset($_GET['wID']) || (isset($_GET['wID']) && ((int)$_GET['wID'] ===  $Qproducts_length->valueInt('products_length_id')))) && !isset($trInfo) && (substr($action, 0, 3) != 'new')) {
        $trInfo = new ObjectInfo($Qproducts_length->toArray());
      }

      $products_length_class_title = ProductsLengthAdmin::getTitle($Qproducts_length->valueInt('products_length_class_to_id'), $CLICSHOPPING_Language->getID());

?>
              <th scope="row"><?php echo $Qproducts_length->valueInt('products_length_class_id'); ?></th>
              <td><?php echo $Qproducts_length->value('products_length_class_key'); ?></td>
              <td><?php echo $Qproducts_length->value('products_length_class_title'); ?></td>
              <td><?php echo $products_length_class_title; ?></td>
              <td><?php echo $Qproducts_length->value('products_length_class_rule'); ?></td>
              <td class="text-md-right">
<?php
      echo HTML::link($CLICSHOPPING_ProductsLength->link('ClassEdit&page=' . $_GET['page'] . '&wID=' .  $Qproducts_length->valueInt('products_length_class_id') . '&tID=' .  $Qproducts_length->valueInt('products_length_class_to_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_ProductsLength->getDef('icon_edit')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_ProductsLength->link('ProductsLengthEdit&page=' . $_GET['page'] . '&wID=' .  $Qproducts_length->valueInt('products_length_class_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/copy.gif', $CLICSHOPPING_ProductsLength->getDef('icon_edit_class_title')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_ProductsLength->link('ClassDelete&page=' . $_GET['page'] . '&wID=' .  $Qproducts_length->valueInt('products_length_class_id') . '&tID=' .  $Qproducts_length->valueInt('products_length_class_to_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_ProductsLength->getDef('icon_delete')));
      echo '&nbsp;';
?>
              </td>
            </tr>

<?php
    } // end while
  }
?>
        </tbody>
      </table></td>
    </table>

<?php
  if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qproducts_length->getPageSetLabel($CLICSHOPPING_ProductsLength->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qproducts_length->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
  }
?>
</div>
