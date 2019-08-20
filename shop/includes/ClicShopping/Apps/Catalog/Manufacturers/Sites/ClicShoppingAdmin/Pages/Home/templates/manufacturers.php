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

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Manufacturers = Registry::get('Manufacturers');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $languages = $CLICSHOPPING_Language->getLanguages();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/manufacturers.gif', $CLICSHOPPING_Manufacturers->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Manufacturers->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Manufacturers->getDef('button_new'), null, $CLICSHOPPING_Manufacturers->link('Edit'), 'success');
  echo HTML::form('delete_all', $CLICSHOPPING_Manufacturers->link('Manufacturers&DeleteAll&page=' . $page));
?>
              <a onclick="$('delete').prop('action', ''); $('form').submit();"
                 class="button"><span><?php echo HTML::button($CLICSHOPPING_Manufacturers->getDef('button_delete'), null, null, 'danger'); ?></span></a>
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
          <td width="1" class="text-md-center"><input type="checkbox"
                                                      onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
          </td>
          <td></td>
          <td><?php echo $CLICSHOPPING_Manufacturers->getDef('table_heading_manufacturers'); ?></td>
          <td class="text-md-center"><?php echo $CLICSHOPPING_Manufacturers->getDef('table_heading_status'); ?></td>
          <td class="text-md-right"><?php echo $CLICSHOPPING_Manufacturers->getDef('table_heading_action'); ?>&nbsp;
          </td>
        </tr>
        </thead>
        <tbody>
        <?php
          $Qmanufacturers = $CLICSHOPPING_Manufacturers->db->prepare('select  SQL_CALC_FOUND_ROWS manufacturers_id,
                                                                                   manufacturers_name,
                                                                                   manufacturers_image,
                                                                                   date_added,
                                                                                   last_modified,
                                                                                   manufacturers_status
                                                          from :table_manufacturers
                                                          order by manufacturers_name
                                                          limit :page_set_offset,
                                                                :page_set_max_results
                                                         ');

          $Qmanufacturers->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qmanufacturers->execute();

          $listingTotalRow = $Qmanufacturers->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qmanufacturers->fetch()) {
              if ((!isset($_GET['mID']) || (isset($_GET['mID']) && ((int)$_GET['mID'] == $Qmanufacturers->valueInt('manufacturers_id')))) && !isset($mInfo)) {

                $Qproducts = $CLICSHOPPING_Manufacturers->db->get('products', 'count(*) as products_count', ['manufacturers_id' => $Qmanufacturers->valueInt('manufacturers_id')]);

                $mInfo_array = array_merge($Qmanufacturers->toArray(), $Qproducts->toArray());
                $mInfo = new ObjectInfo($mInfo_array);
              }
              ?>
              <td>
                <?php
                  if (isset($_POST['selected'])) {
                    ?>
                    <input type="checkbox" name="selected[]"
                           value="<?php echo $Qmanufacturers->valueInt('manufacturers_id'); ?>" checked="checked"/>
                    <?php
                  } else {
                    ?>
                    <input type="checkbox" name="selected[]"
                           value="<?php echo $Qmanufacturers->valueInt('manufacturers_id'); ?>"/>
                    <?php
                  }
                ?>
              </td>
              <td><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qmanufacturers->value('manufacturers_image'), $Qmanufacturers->value('manufacturers_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
              <th scope="row"><?php echo $Qmanufacturers->value('manufacturers_name'); ?></th>
              <td class="text-md-center">
                <?php
                  if ($Qmanufacturers->value('manufacturers_status') == '0') {
                    echo '<a href="' . $CLICSHOPPING_Manufacturers->link('Manufacturers&SetFlag&page=' . $page . '&flag=1&id=' . $Qmanufacturers->valueInt('manufacturers_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                  } else {
                    echo '<a href="' . $CLICSHOPPING_Manufacturers->link('Manufacturers&SetFlag&page=' . $page . '&flag=0&id=' . $Qmanufacturers->valueInt('manufacturers_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                  }
                ?>
              </td>
              <td class="text-md-right">
                <?php
                  echo '<a href="' . $CLICSHOPPING_Manufacturers->link('Edit&page=' . $page . '&mID=' . $Qmanufacturers->valueInt('manufacturers_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Manufacturers->getDef('icon_edit')) . '</a>';
                  echo '&nbsp;';
                ?>
              </td>
              </tr>
              <?php
            } //end while
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
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qmanufacturers->getPageSetLabel($CLICSHOPPING_Manufacturers->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $Qmanufacturers->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>