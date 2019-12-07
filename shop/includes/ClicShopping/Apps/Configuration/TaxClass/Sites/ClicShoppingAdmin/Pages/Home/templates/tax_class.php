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

  ;

  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_TaxClass = Registry::get('TaxClass');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/tax_classes.gif', $CLICSHOPPING_TaxClass->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TaxClass->getDef('heading_title'); ?></span>
          <span
            class="col-md-7 text-md-right"><?php echo HTML::button($CLICSHOPPING_TaxClass->getDef('button_insert'), null, $CLICSHOPPING_TaxClass->link('Insert&page=' . $page), 'success'); ?></span>
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
          <th><?php echo $CLICSHOPPING_TaxClass->getDef('table_heading_tax_classes'); ?></th>
          <th><?php echo $CLICSHOPPING_TaxClass->getDef('table_heading_tax_description'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_TaxClass->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
          $Qclasse = $CLICSHOPPING_TaxClass->db->prepare('select  SQL_CALC_FOUND_ROWS  tax_class_id,
                                                                         tax_class_title,
                                                                         tax_class_description,
                                                                         last_modified,
                                                                         date_added
                                            from :table_tax_class
                                            order by tax_class_title
                                            limit :page_set_offset,
                                                  :page_set_max_results
                                            ');

          $Qclasse->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qclasse->execute();

          $listingTotalRow = $Qclasse->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qclasse->fetch()) {
              if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $Qclasse->valueInt('tax_class_id')))) && !isset($tcInfo)) {
                $tcInfo = new ObjectInfo($Qclasse->toArray());
              }
              ?>
              <th scope="row"><?php echo $Qclasse->value('tax_class_title'); ?></th>
              <td><?php echo $Qclasse->value('tax_class_description'); ?></td>
              <td class="text-md-right">
                <?php
                  echo '<a href="' . $CLICSHOPPING_TaxClass->link('Edit&page=' . $page . '&tID=' . $Qclasse->valueInt('tax_class_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_TaxClass->getDef('icon_edit')) . '</a>';
                  echo '&nbsp;';
                  echo '<a href="' . $CLICSHOPPING_TaxClass->link('Delete&page=' . $page . '&tID=' . $Qclasse->valueInt('tax_class_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_TaxClass->getDef('icon_delete')) . '</a>';
                  echo '&nbsp;';
                ?>
              </td>
              </tr>

              <?php
            } // end while
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
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qclasse->getPageSetLabel($CLICSHOPPING_TaxClass->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"> <?php echo $Qclasse->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>

</div>
