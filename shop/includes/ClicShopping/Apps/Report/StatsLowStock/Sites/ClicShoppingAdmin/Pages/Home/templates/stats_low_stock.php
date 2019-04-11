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
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $CLICSHOPPING_StatsLowStock = Registry::get('StatsLowStock');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_StatsLowStock = Registry::get('StatsLowStock');
?>

  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/stats_customers.gif', $CLICSHOPPING_StatsLowStock->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_StatsLowStock->getDef('heading_title'); ?></span>
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
            <th width="20"></th>
            <th width="50"></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_products'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_model'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_wahrehouse_time_replenishment'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_warehouse'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_warehouse_row'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_warehouse_level'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_qty_left'); ?></th>
            <th class="text-md-center"><?php echo $CLICSHOPPING_StatsLowStock->getDef('table_heading_action'); ?></th>
          </tr>
          </thead>
          <tbody>
<?php
  $Qcheck = $CLICSHOPPING_Db->query("show columns from :table_products like 'products_warehouse_time_replenishment'");

  if ($Qcheck->fetch() === false) {
    $Qproducts = $CLICSHOPPING_StatsLowStock->db->prepare('select  SQL_CALC_FOUND_ROWS  p.products_id,
                                                                                        p.products_quantity,
                                                                                        p.products_model,
                                                                                        pd.products_name,
                                                                                        p.products_image,
                                                                                        p.products_packaging
                                                           from :table_products p,
                                                                :table_products_description pd
                                                           where p.products_id = pd.products_id
                                                           and pd.language_id = :language_id
                                                           and p.products_quantity < :products_quantity
                                                           group by pd.products_id
                                                           order by pd.products_name ASC
                                                           limit :page_set_offset,
                                                                :page_set_max_results
                                                          ');
  } else {
    $Qproducts = $CLICSHOPPING_StatsLowStock->db->prepare('select SQL_CALC_FOUND_ROWS  p.products_id,
                                                                                      p.products_quantity,
                                                                                      p.products_model,
                                                                                      pd.products_name,
                                                                                      p.products_image,
                                                                                      p.products_warehouse_time_replenishment,
                                                                                      p.products_warehouse,
                                                                                      p.products_warehouse_row,
                                                                                      p.products_warehouse_level_location,
                                                                                      p.products_packaging
                                                            from :table_products p,
                                                            :table_products_description pd
                                                            where p.products_id = pd.products_id
                                                            and pd.language_id = :language_id
                                                            and p.products_quantity < :products_quantity
                                                            group by pd.products_id
                                                            order by pd.products_name ASC
                                                            limit :page_set_offset,
                                                            :page_set_max_results
                                                            ');
  }

  $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());
  $Qproducts->bindInt(':products_quantity', STOCK_REORDER_LEVEL);
  $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
  $Qproducts->execute();

  $listingTotalRow = $Qproducts->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

    while ($Qproducts->fetch()) {
      $rows++;

      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
?>
                <tr>
                  <td scope="row" width="50px"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Preview&Preview&pID=' . $Qproducts->valueInt('products_id') . '?page=' . $_GET['page']), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_StatsLowStock->getDef('icon_preview'))); ?></td>
                  <td><?php echo  HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qproducts->value('products_image'), $Qproducts->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
                  <th>&nbsp;<?php echo $Qproducts->value('products_name'); ?></th>
                  <td><?php echo $Qproducts->value('products_model'); ?></td>
<?php
      if ($check === false) {
?>

                  <td></td>
                  <td></td>
                  <td></td>
<?php
      } else {
?>
                  <td><?php echo $Qproducts->value('products_warehouse_time_replenishment'); ?></td>
                  <td><?php echo $Qproducts->value('products_warehouse'); ?></td>
                  <td><?php echo $Qproducts->value('products_warehouse_row'); ?></td>
<?php
      }
?>
                  <td><?php echo $Qproducts->value('products_warehouse_level_location'); ?></td>
                  <td class="text-md-center"><strong><?php echo  $Qproducts->value('products_quantity'); ?></strong></td>
                  <td class="text-md-right"><?php echo HTML::link(CLICSHOPPING::link(null ,'A&Catalog\Products&Products&action=new_product&pID=' . $Qproducts->valueInt('products_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_StatsLowStock->getDef('icon_edit'))); ?></td>
                </tr>
<?php
    }
  } // end $listingTotalRow
?>
          </tbody>
        </table>
      </td>
    </table>
<?php
  if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qproducts->getPageSetLabel($CLICSHOPPING_StatsLowStock->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qproducts->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
} // end $listingTotalRow
?>
  </div>
