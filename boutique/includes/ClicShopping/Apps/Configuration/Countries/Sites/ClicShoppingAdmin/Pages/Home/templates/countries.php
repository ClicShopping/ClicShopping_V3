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
  use ClicShopping\OM\Registry;;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Countries = Registry::get('Countries');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Language = Registry::get('Language');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/countries.gif', $CLICSHOPPING_Countries->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Countries->getDef('heading_title'); ?></span>

          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Countries->getDef('button_insert'), null, $CLICSHOPPING_Countries->link('Insert&page=' . $_GET['page']), 'success');
  echo HTML::form('update_all', $CLICSHOPPING_Countries->link('Countries&UpdateAll&page=' . $_GET['page']));
?>
            <a onclick="$('update').prop('action', ''); $('form').submit();" class="button"><?php echo  HTML::button($CLICSHOPPING_Countries->getDef('button_update'), null, null, 'warning');  ?></a>&nbsp;
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
          <td width="1" clas="text-md-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
          <td><?php echo $CLICSHOPPING_Countries->getDef('table_heading_country_name'); ?></td>
          <td class="text-md-center"><?php echo $CLICSHOPPING_Countries->getDef('table_heading_country_status'); ?></td>
          <td class="text-md-center" colspan="2"><?php echo $CLICSHOPPING_Countries->getDef('table_heading_country_code'); ?></td>
          <td class="text-md-right"><?php echo $CLICSHOPPING_Countries->getDef('table_heading_action'); ?>&nbsp;</td>
        </tr>
        </thead>
        <tbody>
<?php
  $Qcountries = $CLICSHOPPING_Countries->db->prepare('select  SQL_CALC_FOUND_ROWS countries_id,
                                                                           countries_name,
                                                                           countries_iso_code_2,
                                                                           countries_iso_code_3,
                                                                           status,
                                                                           address_format_id
                                              from :table_countries
                                              order by countries_name
                                              limit :page_set_offset, :page_set_max_results
                                              ');

  $Qcountries->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
  $Qcountries->execute();

  $listingTotalRow = $Qcountries->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

    while ($Qcountries->fetch()) {
      if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] == $Qcountries->valueInt('countries_id')))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
        $cInfo = new ObjectInfo($Qcountries->toArray());
      }
?>
            <td>
<?php
      if (($Qcountries->value('selected'))) {
?>
        <input type="checkbox" name="selected[]" value="<?php echo $Qcountries->valueInt('countries_id'); ?>" checked="checked" />
<?php
      } else {
?>
        <input type="checkbox" name="selected[]" value="<?php echo $Qcountries->valueInt('countries_id'); ?>" />
<?php
      }
?>
            </td>
            <td><?php echo $Qcountries->value('countries_name'); ?></td>
            <td class="text-md-center">
<?php
      if ($Qcountries->valueInt('status') == 1) {
        echo HTML::link($CLICSHOPPING_Countries->link('Countries&SetFlag&flag=0&cID=' . $Qcountries->valueInt('countries_id') . '&page=' . $_GET['page']),'<i class="fas fa-check fa-lg" aria-hidden="true"></i>');
//        echo '<a href="' . $CLICSHOPPING_Countries->link('Countries&SetFlag&cID=' . $Qcountries->valueInt('countries_id') . '&flag=0&page=' . $_GET['page']) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
      } else {
        echo HTML::link($CLICSHOPPING_Countries->link('Countries&SetFlag&flag=1&cID=' . $Qcountries->valueInt('countries_id') . '&page=' . $_GET['page']),'<i class="fas fa-times fa-lg" aria-hidden="true"></i>');
//         echo '<a href="' . $CLICSHOPPING_Countries->link('Countries&SetFlag&cID=' . $Qcountries->valueInt('countries_id') . '&flag=1&page=' . $_GET['page']) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
      }
?>
            </td>
            <td class="text-md-center" width="40"><?php echo $Qcountries->value('countries_iso_code_2'); ?></td>
            <td class="text-md-center" width="40"><?php echo $Qcountries->value('countries_iso_code_3'); ?></td>
            <td class="text-md-right">
<?php
      echo HTML::link($CLICSHOPPING_Countries->link('Edit&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Countries->getDef('icon_edit')));
//      echo '<a href="' . $CLICSHOPPING_Countries->link('Edit&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Countries->getDef('icon_edit')) . '</a>' ;
      echo '&nbsp;';
      echo HTML::link($CLICSHOPPING_Countries->link('Delete&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Countries->getDef('icon_delete')));
//      echo '<a href="' . $CLICSHOPPING_Countries->link('Delete&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Countries->getDef('icon_delete')) . '</a>';
?>
            </td>
            </tr>
<?php
    } // end while
?>
        </tbody>
      </table>
<?php
  } // end $listingTotalRow
?>
    </table></td>
  </table>
  </form>
<?php
  if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qcountries->getPageSetLabel($CLICSHOPPING_Countries->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qcountries->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
    <?php
  } // end $listingTotalRow
?>
</div>
