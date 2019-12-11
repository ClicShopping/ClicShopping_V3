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

  $CLICSHOPPING_Suppliers = Registry::get('Suppliers');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/suppliers.gif', $CLICSHOPPING_Suppliers->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Suppliers->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Suppliers->getDef('button_new'), null, $CLICSHOPPING_Suppliers->link('Edit'), 'success');
  echo HTML::form('delete_all', $CLICSHOPPING_Suppliers->link('Suppliers&DeleteAll&page=' . $page));
?>
              <a onclick="$('delete').prop('action', ''); $('form').submit();"
                 class="button"><span><?php echo HTML::button($CLICSHOPPING_Suppliers->getDef('button_delete'), null, null, 'danger'); ?></span></a>
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
          <th width="1" class="text-md-center"><input type="checkbox"
                                                      onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
          </th>
          <th></th>
          <th><?php echo $CLICSHOPPING_Suppliers->getDef('table_heading_suppliers'); ?></th>
          <th><?php echo $CLICSHOPPING_Suppliers->getDef('table_heading_manager'); ?></th>
          <th><?php echo $CLICSHOPPING_Suppliers->getDef('table_heading_phone'); ?></th>
          <th><?php echo $CLICSHOPPING_Suppliers->getDef('table_heading_fax'); ?></th>
          <th><?php echo $CLICSHOPPING_Suppliers->getDef('table_heading_email_address'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Suppliers->getDef('table_heading_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Suppliers->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
          $Qsuppliers = $CLICSHOPPING_Suppliers->db->prepare('select  SQL_CALC_FOUND_ROWS  *
                                                from :table_suppliers
                                                order by suppliers_name
                                                limit :page_set_offset, :page_set_max_results
                                                ');

          $Qsuppliers->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qsuppliers->execute();

          $listingTotalRow = $Qsuppliers->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qsuppliers->fetch()) {
              if ((!isset($_GET['mID']) || (isset($_GET['mID']) && ((int)$_GET['mID'] == $Qsuppliers->valueInt('suppliers_id')))) && !isset($mInfo)) {

                $Qproducts = $CLICSHOPPING_Suppliers->db->get('products', 'count(*) as products_count', ['suppliers_id' => $Qsuppliers->valueInt('suppliers_id')]);

                $mInfo_array = array_merge($Qsuppliers->toArray(), $Qproducts->toArray());
                $mInfo = new ObjectInfo($mInfo_array);
              }
              ?>
              <tr>
                <td>
                  <?php
                    if (isset($_POST['selected'])) {
                      ?>
                      <input type="checkbox" name="selected[]"
                             value="<?php echo $Qsuppliers->valueInt('suppliers_id'); ?>" checked="checked"/>
                      <?php
                    } else {
                      ?>
                      <input type="checkbox" name="selected[]"
                             value="<?php echo $Qsuppliers->valueInt('suppliers_id'); ?>"/>
                      <?php
                    }
                  ?>
                </td>
                <td><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qsuppliers->value('suppliers_image'), $Qsuppliers->value('suppliers_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
                <th scope="row"><?php echo $Qsuppliers->value('suppliers_name'); ?></th>
                <td><?php echo $Qsuppliers->value('suppliers_manager'); ?></td>
                <td><?php echo $Qsuppliers->value('suppliers_phone'); ?></td>
                <td><?php echo $Qsuppliers->value('suppliers_fax'); ?></td>
                <td><?php echo $Qsuppliers->value('suppliers_email_address'); ?></td>
                <td class="text-md-center">
                  <?php
                    if ($Qsuppliers->valueInt('suppliers_status') == 0) {
                      echo '<a href="' . $CLICSHOPPING_Suppliers->link('Suppliers&SetFlag&page=' . $page . '&flag=1&id=' . $Qsuppliers->valueInt('suppliers_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                    } else {
                      echo '<a href="' . $CLICSHOPPING_Suppliers->link('Suppliers&SetFlag&page=' . $page . '&flag=0&id=' . $Qsuppliers->valueInt('suppliers_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                    }
                  ?>
                </td>
                <td class="text-md-right">
                  <?php
                    echo '<a href="' . $CLICSHOPPING_Suppliers->link('Edit&page=' . $page . '&mID=' . $Qsuppliers->valueInt('suppliers_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Suppliers->getDef('icon_edit')) . '</a>';
                    echo '&nbsp;';
                  ?>
                </td>
              </tr>
              <?php
            } // end while
          } // end $listingTotalRow
        ?>
        </form><!-- end form delete all -->
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
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qsuppliers->getPageSetLabel($CLICSHOPPING_Suppliers->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $Qsuppliers->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    }
  ?>


</div>

