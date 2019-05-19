<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_StatsProductsExpected = Registry::get('StatsProductsExpected');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $Qupdate = $CLICSHOPPING_Db->prepare('update :table_products
                                  set products_date_available = :products_date_available
                                  where to_days(now()) > to_days(products_date_available)
                                ');
  $Qupdate->bindValue(':products_date_available', '');
  $Qupdate->execute();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/products_expected.gif', $CLICSHOPPING_StatsProductsExpected->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_StatsProductsExpected->getDef('heading_title'); ?></span>
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
          <th></th>
          <th></th>
          <th><?php echo $CLICSHOPPING_StatsProductsExpected->getDef('table_heading_products'); ?></th>
          <th
            class="text-md-center"><?php echo $CLICSHOPPING_StatsProductsExpected->getDef('table_heading_date_expected'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_StatsProductsExpected->getDef('table_heading_action'); ?>
            &nbsp;
          </th>
        </tr>
        </thead>
        <tbody>
        <?php

          $Qproducts = $CLICSHOPPING_StatsProductsExpected->db->prepare('select  SQL_CALC_FOUND_ROWS  pd.products_id,
                                                                                        pd.products_name,
                                                                                        p.products_image,
                                                                                        p.products_date_available
                                                           from :table_products_description pd,
                                                                :table_products p
                                                           where p.products_id = pd.products_id
                                                           and (p.products_date_available = :products_date_available
                                                               or p.products_date_available <> :products_date_available1)
                                                           and pd.language_id = :language_id
                                                           order by p.products_date_available DESC
                                                           limit :page_set_offset, :page_set_max_results
                                                           ');

          $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());
          $Qproducts->bindInt(':products_date_available', '');
          $Qproducts->bindInt(':products_date_available1', '');
          $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qproducts->execute();

          $listingTotalRow = $Qproducts->getPageSetTotalRows();

          if ($listingTotalRow > 0) {
            while ($products = $Qproducts->fetch()) {
              $rows++;

              if (strlen($rows) < 2) {
                $rows = '0' . $rows;
              }
              ?>
              <tr>
                <td scope="row"
                    width="50px"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Preview&Preview&pID=' . $Qproducts->valueInt('products_id') . '?page=' . $_GET['page']), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_StatsProductsExpected->getDef('icon_preview'))); ?></td>
                <td
                  class="dataTableContent"><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qproducts->value('products_image'), $Qproducts->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
                <td class="dataTableContent"><?php echo $Qproducts->value('products_name'); ?></td>
                <td
                  class="dataTableContent text-md-center"><?php echo DateTime::toShort($products['products_date_available']); ?></td>
                <td
                  class="dataTableContent text-md-right"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Products&Products&pID=' . $pInfo->products_id . '&action=new_product'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_StatsProductsExpected->getDef('image_edit'))); ?></td>
              </tr>
              <?php
            } // end $listingTotalRow
          }
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
          <div
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qproducts->getPageSetLabel($CLICSHOPPING_StatsProductsExpected->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $Qproducts->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>


