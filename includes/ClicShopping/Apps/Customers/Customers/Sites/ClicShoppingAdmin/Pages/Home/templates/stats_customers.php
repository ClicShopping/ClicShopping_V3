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

  $CLICSHOPPING_Customers = Registry::get('Customers');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/stats_customers.gif', $CLICSHOPPING_Customers->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-3 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Customers->getDef('heading_title'); ?></span>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <table
    id="table"
    data-toggle="table"
    data-sort-name="number"
    data-sort-order="asc"
    data-toolbar="#toolbar"
    data-buttons-class="primary"
    data-show-toggle="true"
    data-show-columns="true"
    data-mobile-responsive="true"
    data-show-export="true">

    <thead class="dataTableHeadingRow">
      <tr>
        <th data-field="number"><?php echo $CLICSHOPPING_Customers->getDef('table_heading_number'); ?></th>
        <th data-field="customer" data-sortable="true"><?php echo $CLICSHOPPING_Customers->getDef('table_heading_customers'); ?></th>
        <th data-field="total_purchased" data-sortable="true"><?php echo $CLICSHOPPING_Customers->getDef('table_heading_total_purchased'); ?></th>
        <th data-field="action" data-switchable="false" class="text-md-center" width="20"><?php echo $CLICSHOPPING_Customers->getDef('table_heading_action'); ?>&nbsp;
        </th>
      </tr>
    </thead>
    <tbody>
    <?php
      $Qcustomers = $CLICSHOPPING_Customers->db->prepare('select SQL_CALC_FOUND_ROWS  c.customers_firstname,
                                                                                      c.customers_lastname,
                                                                                      c.customers_group_id,
                                                                                      sum(op.products_quantity * op.final_price) as ordersum
                                                          from :table_customers c,
                                                               :table_orders_products op,
                                                               :table_orders o
                                                          where c.customers_id = o.customers_id
                                                          and o.orders_id = op.orders_id
                                                          group by c.customers_firstname,
                                                                   c.customers_lastname,
                                                                   c.customers_group_id
                                                          order by ordersum desc
                                                          limit :page_set_offset,
                                                                :page_set_max_results
                                                          ');

      $Qcustomers->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $Qcustomers->execute();

      $listingTotalRow = $Qcustomers->getPageSetTotalRows();

      if ($listingTotalRow > 0) {

        $rows = 0;

        while ($Qcustomers->fetch()) {
          $rows++;

          if (strlen($rows) < 2) {
            $rows = '0' . $rows;
          }
          ?>
          <tr>
            <td><?php echo $rows; ?>.</td>
            <td><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Customers\Customers&Customers&Customers&search=' . $Qcustomers->value('customers_lastname')), $Qcustomers->value('customers_firstname') . ' ' . $Qcustomers->value('customers_lastname')); ?></td>
            <td
              class="text-md-right"><?php echo $CLICSHOPPING_Currencies->format($Qcustomers->valueInt('ordersum')); ?>&nbsp;
            </td>
            <?php
              if ($Qcustomers->valueInt('customers_group_id') > 0) {
                ?>
                <td
                  class="text-md-right"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Customers\Customers%5CCustomers&Customers&search=' . $Qcustomers->value('customers_lastname')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/client_b2b.gif', $CLICSHOPPING_Customers->getDef('icon_edit_customer'))); ?></td>
                <?php
              } else {
                ?>
                <td
                  class="text-md-right"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Customers\Customers&Customers&search=' . $Qcustomers->value('customers_lastname')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/client_b2c.gif', $CLICSHOPPING_Customers->getDef('icon_edit_customer'))); ?></td>
                <?php
              }
            ?>
          </tr>
          <?php
        } // end while
      } // end $listingTotalRow
    ?>
    </tbody>
  </table>
  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qcustomers->getPageSetLabel($CLICSHOPPING_Customers->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $Qcustomers->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>

