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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_OrdersStatusInvoice = Registry::get('OrdersStatusInvoice');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Language = Registry::get('Language');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/configuration_26.gif', $CLICSHOPPING_OrdersStatusInvoice->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_OrdersStatusInvoice->getDef('heading_title'); ?></span>
          <span
            class="col-md-7 text-md-right"><?php echo HTML::button($CLICSHOPPING_OrdersStatusInvoice->getDef('button_insert'), null, $CLICSHOPPING_OrdersStatusInvoice->link('Insert'), 'success', null, 'xs'); ?></span>
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
          <th><?php echo $CLICSHOPPING_OrdersStatusInvoice->getDef('table_heading_invoice_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_OrdersStatusInvoice->getDef('table_heading_action'); ?>
            &nbsp;
          </th>
        </tr>
        </thead>
        <tbody>
        <?php
          $QordersStatusInvoice = $CLICSHOPPING_OrdersStatusInvoice->db->prepare('select  SQL_CALC_FOUND_ROWS  *
                                                                       from :table_orders_status_invoice
                                                                       where language_id = :language_id
                                                                       order by orders_status_invoice_id
                                                                       limit :page_set_offset,
                                                                             :page_set_max_results
                                                                      ');

          $QordersStatusInvoice->bindInt(':language_id', $CLICSHOPPING_Language->getId());
          $QordersStatusInvoice->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $QordersStatusInvoice->execute();

          $listingTotalRow = $QordersStatusInvoice->getPageSetTotalRows();

          if ($listingTotalRow > 0) {
          while ($QordersStatusInvoice->fetch()) {

          if ((!isset($_GET['oID']) || (isset($_GET['oID']) && ((int)$_GET['oID'] === $QordersStatusInvoice->valueInt('orders_status_invoice_id'))))) {
            $oInfo = new ObjectInfo($QordersStatusInvoice->toArray());
          }

          if (DEFAULT_ORDERS_STATUS_INVOICE_ID == $QordersStatusInvoice->valueInt('orders_status_invoice_id')) {
            echo '                <th scope="row"><strong>' . $QordersStatusInvoice->value('orders_status_invoice_name') . ' (' . $CLICSHOPPING_OrdersStatusInvoice->getDef('text_default') . ')</strong></th>' . "\n";
          } else {
            echo '                <th scope="row">' . $QordersStatusInvoice->value('orders_status_invoice_name') . '</th>' . "\n";
          }
        ?>
        <td class="text-md-right">
          <?php
            if ($QordersStatusInvoice->valueInt('orders_status_invoice_id') > 4) {
              echo '<a href="' . $CLICSHOPPING_OrdersStatusInvoice->link('Delete&page=' . $page . '&oID=' . $QordersStatusInvoice->valueInt('orders_status_invoice_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_OrdersStatusInvoice->getDef('icon_delete')) . '</a>';
            }

            echo '&nbsp;';
            echo '<a href="' . $CLICSHOPPING_OrdersStatusInvoice->link('Edit&page=' . $page . '&oID=' . $QordersStatusInvoice->valueInt('orders_status_invoice_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_OrdersStatusInvoice->getDef('icon_edit')) . '</a>';
            echo '&nbsp;';
          ?>
        </td>
        </tbody>
        </tr>
        <?php
          }
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
          <div
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $QordersStatusInvoice->getPageSetLabel($CLICSHOPPING_OrdersStatusInvoice->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $QordersStatusInvoice->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>