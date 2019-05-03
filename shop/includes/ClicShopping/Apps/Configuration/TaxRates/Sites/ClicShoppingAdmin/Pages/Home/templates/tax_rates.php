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

  use ClicShopping\Sites\ClicShoppingAdmin\Tax;

  $CLICSHOPPING_TaxRates = Registry::get('TaxRates');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/tax_rates.gif', $CLICSHOPPING_TaxRates->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_TaxRates->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right"><?php echo HTML::button($CLICSHOPPING_TaxRates->getDef('button_insert'), null, $CLICSHOPPING_TaxRates->link('Insert&page=' . $page), 'success'); ?></span>
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
          <th><?php echo $CLICSHOPPING_TaxRates->getDef('table_heading_tax_rate_priority'); ?></th>
          <th><?php echo $CLICSHOPPING_TaxRates->getDef('table_heading_tax_class_title'); ?></th>
          <th><?php echo $CLICSHOPPING_TaxRates->getDef('table_heading_zone'); ?></th>
          <th><?php echo $CLICSHOPPING_TaxRates->getDef('table_heading_tax_rate'); ?></th>
          <th><?php echo $CLICSHOPPING_TaxRates->getDef('table_heading_tax_description'); ?></th>
          <th><?php echo $CLICSHOPPING_TaxRates->getDef('table_heading_code_tax_erp'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_TaxRates->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
<?php

  $Qrates = $CLICSHOPPING_TaxRates->db->prepare('select  SQL_CALC_FOUND_ROWS  r.tax_rates_id,
                                                                     z.geo_zone_id,
                                                                     z.geo_zone_name,
                                                                     tc.tax_class_title,
                                                                     tc.tax_class_id,
                                                                     r.tax_priority,
                                                                     r.tax_rate,
                                                                     r.tax_description,
                                                                     r.date_added,
                                                                     r.last_modified,
                                                                     r.code_tax_erp
                                          from :table_tax_class tc,
                                               :table_tax_rates r left join :table_geo_zones z on r.tax_zone_id = z.geo_zone_id
                                          where r.tax_class_id = tc.tax_class_id
                                          limit :page_set_offset,
                                                :page_set_max_results
                                          ');

  $Qrates->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
  $Qrates->execute();

  $listingTotalRow = $Qrates->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

    while ($Qrates->fetch()) {
      if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ((int)$_GET['tID'] ===  $Qrates->valueInt('tax_rates_id')))) && !isset($trInfo)) {
        $trInfo = new ObjectInfo($Qrates->toArray());
      }
?>
              <th scope="row"><?php echo  $Qrates->valueInt('tax_priority'); ?></th>
              <td><?php echo $Qrates->value('tax_class_title'); ?></td>
              <td><?php echo $Qrates->value('geo_zone_name'); ?></td>
              <td><?php echo Tax::displayTaxRateValue($Qrates->valueDecimal('tax_rate')); ?></td>
              <td><?php echo  $Qrates->value('tax_description'); ?></td>
              <td><?php echo  $Qrates->value('code_tax_erp'); ?></td>
              <td class="text-md-right">
<?php
      echo HTML::link($CLICSHOPPING_TaxRates->link('Edit&page=' . $page . '&tID=' .  $Qrates->valueInt('tax_rates_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_TaxRates->getDef('icon_edit')));
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_TaxRates->link('Delete&page=' . $page . '&tID=' .  $Qrates->valueInt('tax_rates_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_TaxRates->getDef('icon_delete')));
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
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qrates->getPageSetLabel($CLICSHOPPING_TaxRates->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qrates->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
  }
?>
</div>
