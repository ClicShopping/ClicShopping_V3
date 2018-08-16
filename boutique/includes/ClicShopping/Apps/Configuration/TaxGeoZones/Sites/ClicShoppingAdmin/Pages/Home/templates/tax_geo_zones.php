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

  $CLICSHOPPING_TaxGeoZones = Registry::get('TaxGeoZones');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  if (!isset($_GET['zpage']) || !is_numeric($_GET['zpage'])) {
    $_GET['zpage'] = 1;
  }
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/geo_zones.gif', $CLICSHOPPING_TaxGeoZones->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TaxGeoZones->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right"><?php echo HTML::button($CLICSHOPPING_TaxGeoZones->getDef('button_insert'), null, $CLICSHOPPING_TaxGeoZones->link('Insert&page=' . $_GET['page']), 'success'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <table class="table table-sm table-hover table-striped">
    <thead>
    <tr class="dataTableHeadingRow">
      <th><?php echo $CLICSHOPPING_TaxGeoZones->getDef('table_heading_tax_zones'); ?></th>
      <th><?php echo $CLICSHOPPING_TaxGeoZones->getDef('table_heading_tax_description'); ?></th>
      <th><?php echo $CLICSHOPPING_TaxGeoZones->getDef('table_heading_number_zone'); ?></th>
      <th class="text-md-right"><?php echo $CLICSHOPPING_TaxGeoZones->getDef('table_heading_action'); ?>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
<?php
  $Qzones = $CLICSHOPPING_TaxGeoZones->db->prepare('select  SQL_CALC_FOUND_ROWS  geo_zone_id,
                                                                           geo_zone_name,
                                                                           geo_zone_description,
                                                                           last_modified,
                                                                           date_added
                                             from :table_geo_zones
                                             order by geo_zone_name
                                             limit :page_set_offset,
                                                  :page_set_max_results
                                            ');

  $Qzones->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN, 'zpage');
  $Qzones->execute();

  $listingTotalRow = $Qzones->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

    while ($Qzones->fetch()) {
      if ((!isset($_GET['zID']) || (isset($_GET['zID']) && ((int)$_GET['zID'] === $Qzones->valueInt('geo_zone_id')))) && !isset($zInfo) && (substr($action, 0, 3) != 'new')) {

        $Qtotal = $CLICSHOPPING_TaxGeoZones->db->prepare('select count(*) as num_zones
                                                  from :table_zones_to_geo_zones
                                                  where geo_zone_id = :geo_zone_id
                                                  ');
        $Qtotal->bindInt(':geo_zone_id', $Qzones->valueInt('geo_zone_id'));
        $Qtotal->execute();

        $zInfo = new ObjectInfo(array_merge($Qzones->toArray(), $Qtotal->toArray()));
      }
?>
          <th scope="row"><?php echo '<a href="' . $CLICSHOPPING_TaxGeoZones->link('List&zpage=' . $_GET['zpage'] . '&zID=' . $Qzones->valueInt('geo_zone_id')) . '">' . $Qzones->value('geo_zone_name') .'</a>&nbsp;'; ?></th>
          <td><?php echo $Qzones->value('geo_zone_description'); ?></td>
          <td><?php echo $Qzones->valueInt('num_zones'); ?></td>
          <td class="text-md-right">
<?php
  echo '<a href="' . $CLICSHOPPING_TaxGeoZones->link('Edit&zpage=' . $_GET['zpage'] . '&zID=' . $Qzones->valueInt('geo_zone_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_TaxGeoZones->getDef('icon_edit')) . '</a>' ;
  echo '&nbsp;';
  echo '<a href="' . $CLICSHOPPING_TaxGeoZones->link('ListGeo&zpage=' . $_GET['zpage'] . '&zID=' . $Qzones->valueInt('geo_zone_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/geo_zones.gif', $CLICSHOPPING_TaxGeoZones->getDef('image_details')) . '</a>';
  echo '&nbsp;';
  echo '<a href="' . $CLICSHOPPING_TaxGeoZones->link('Delete&zpage=' . $_GET['zpage'] . '&zID=' . $Qzones->valueInt('geo_zone_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_TaxGeoZones->getDef('image_delete')) . '</a>';
  echo '&nbsp;';
?>
          </td>
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
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qzones->getPageSetLabel($CLICSHOPPING_TaxGeoZones->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qzones->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
 <?php
  }
?>
</div>
