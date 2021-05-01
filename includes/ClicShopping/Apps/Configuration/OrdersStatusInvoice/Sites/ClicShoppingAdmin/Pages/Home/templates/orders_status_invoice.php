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
            class="col-md-7 text-end"><?php echo HTML::button($CLICSHOPPING_OrdersStatusInvoice->getDef('button_insert'), null, $CLICSHOPPING_OrdersStatusInvoice->link('Insert'), 'success', null, 'xs'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- //################################################################################################################ -->
  <!-- //                                             LISTING                                                            -->
  <!-- //################################################################################################################ -->

  <table
    id="table"
    data-toggle="table"
    data-icons-prefix="bi"
    data-icons="icons"
    data-sort-name="sort_order"
    data-sort-order="asc"
    data-toolbar="#toolbar"
    data-buttons-class="primary"
    data-show-toggle="true"
    data-show-columns="true"
    data-mobile-responsive="true">

    <thead class="dataTableHeadingRow">
    <tr>
      <th data-field="status"><?php echo $CLICSHOPPING_OrdersStatusInvoice->getDef('table_heading_invoice_status'); ?></th>
      <th data-field="action" data-switchable="false" class="text-end"><?php echo $CLICSHOPPING_OrdersStatusInvoice->getDef('table_heading_action'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
      $QordersStatusInvoice = $CLICSHOPPING_OrdersStatusInvoice->db->prepare('select SQL_CALC_FOUND_ROWS orders_status_invoice_id,
                                                                                                         orders_status_invoice_name
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

      echo '<tr>';

      if (DEFAULT_ORDERS_STATUS_INVOICE_ID == $QordersStatusInvoice->valueInt('orders_status_invoice_id')) {
        echo '                <td><strong>' . $QordersStatusInvoice->value('orders_status_invoice_name') . ' (' . $CLICSHOPPING_OrdersStatusInvoice->getDef('text_default') . ')</strong></td>' . "\n";
      } else {
        echo '                <td>' . $QordersStatusInvoice->value('orders_status_invoice_name') . '</td>' . "\n";
      }
    ?>
      <td class="text-end">
        <?php
          if ($QordersStatusInvoice->valueInt('orders_status_invoice_id') > 4) {
            echo HTML::link($CLICSHOPPING_OrdersStatusInvoice->link('Delete&page=' . $page . '&oID=' . $QordersStatusInvoice->valueInt('orders_status_invoice_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_OrdersStatusInvoice->getDef('icon_delete')));
          }

          echo '&nbsp;';
          echo HTML::link($CLICSHOPPING_OrdersStatusInvoice->link('Edit&page=' . $page . '&oID=' . $QordersStatusInvoice->valueInt('orders_status_invoice_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_OrdersStatusInvoice->getDef('icon_edit')));
          echo '&nbsp;';
        ?>
      </td>
    </tr>
    <?php
      }
    } // end $listingTotalRow
    ?>
  </tbody>
  </table>
  <div class="separator"></div>
  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-start pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $QordersStatusInvoice->getPageSetLabel($CLICSHOPPING_OrdersStatusInvoice->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-end text-end"><?php echo $QordersStatusInvoice->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>