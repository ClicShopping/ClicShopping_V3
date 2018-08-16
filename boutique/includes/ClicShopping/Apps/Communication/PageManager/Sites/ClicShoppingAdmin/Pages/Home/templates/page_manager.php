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
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_PageManager = Registry::get('PageManager');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  $languages = $CLICSHOPPING_Language->getLanguages();
?>

  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/page_manager.gif', $CLICSHOPPING_PageManager->getDef('heading_title'), '40', '40'); ?></span>
            <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_PageManager->getDef('heading_title'); ?></span>
            <span class="col-md-6 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_PageManager->getDef('button_new'), null, $CLICSHOPPING_PageManager->link('SelectPage'), 'success');
  echo HTML::form('delete_all', $CLICSHOPPING_PageManager->link('PageManager&DeleteAll&page=' . $_GET['page']));
?>
              <a onclick="$('delete').prop('action', ''); $('form').submit();" class="button"><span><?php echo HTML::button($CLICSHOPPING_PageManager->getDef('button_delete'), null, null, 'danger'); ?></span></a>
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
              <th width="1" class="text-md-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
              <th><?php echo 'Id'; ?></th>
              <th></th>
              <th><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_pages'); ?></th>
              <th><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_type_page'); ?></th>
<?php
  // Permettre l'affichage des groupes en mode B2B
  if (MODE_B2B_B2C == 'true') {
?>
              <th class="text-md-center"><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_customers_group'); ?></th>
<?php
  }
?>
              <th class="text-md-center"><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_status'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_links_target'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_page_type'); ?></th>
              <th class="text-md-center"><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_sort_order'); ?></th>
              <th class="text-md-right"><?php echo $CLICSHOPPING_PageManager->getDef('table_heading_action'); ?></th>
            </tr>
          </thead>
          <tbody>

<?php
  $Qpages = $CLICSHOPPING_PageManager->db->prepare('select  SQL_CALC_FOUND_ROWS  p.pages_id,
                                                                           p.links_target,
                                                                           p.page_type,
                                                                           p.page_box,
                                                                           p.status,
                                                                           p.sort_order,
                                                                           p.date_added,
                                                                           p.page_date_start,
                                                                           p.page_date_closed,
                                                                           p.last_modified,
                                                                           p.date_status_change,
                                                                           s.pages_title,
                                                                           s.externallink,
                                                                           p.customers_group_id,
                                                                           p.page_general_condition
                                              from :table_pages_manager p,
                                                   :table_pages_manager_description s
                                              where s.language_id= :language_id
                                              and p.pages_id = s.pages_id
                                              order by  p.pages_id
                                              limit :page_set_offset,
                                                    :page_set_max_results
                                              ');

  $Qpages->bindInt(':language_id', $CLICSHOPPING_Language->getId() );
  $Qpages->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
  $Qpages->execute();

  $listingTotalRow = $Qpages->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

  while ($Qpages->fetch()) {

    // Permettre l'affichage des groupes en mode B2B
    if (MODE_B2B_B2C == 'true') {

      $QcustomersGroup = $CLICSHOPPING_PageManager->db->prepare('select customers_group_name
                                                          from :table_customers_groups
                                                          where customers_group_id = :customers_group_id
                                                        ');
      $QcustomersGroup->bindInt(':customers_group_id',  $Qpages->valueInt('customers_group_id'));
      $QcustomersGroup->execute();

      $customers_group = $QcustomersGroup->fetch();

      if ($Qpages->valueInt('customers_group_id') == 99) {
        $customers_group['customers_group_name'] =  $CLICSHOPPING_PageManager->getDef('text_all_groups');
      } elseif ($Qpages->valueInt('customers_group_id') == 0) {
        $customers_group['customers_group_name'] =  $CLICSHOPPING_PageManager->getDef('normal_customer');
      }
    }

    if ((!isset($_GET['bID']) || (isset($_GET['bID']) && ((int)$_GET['bID'] === $Qpages->valueInt('pages_id')))) && !isset($bInfo) && (substr($action, 0, 3) != 'new')) {
      $bInfo = new ObjectInfo($Qpages->toArray());
    }
?>
            <td>
<?php
      if ($Qpages->value('selected')) {
?>
              <input type="checkbox" name="selected[]" value="<?php echo   $Qpages->valueInt('pages_id'); ?>" checked="checked" />
<?php
      } else {
?>
              <input type="checkbox" name="selected[]" value="<?php echo   $Qpages->valueInt('pages_id'); ?>" />
<?php
      }
?>
            </td>
            <td scope="row"><?php echo  $Qpages->valueInt('pages_id'); ?></td>

<?php
      if (!empty($Qpages->valueInt('pages_id')) && $Qpages->valueInt('page_type') == 4 && empty($Qpages->value('externallink')))  {

?>
              <td class="dataTableContent" width="1.25rem;"><?php echo '<a href="' . HTTP::getShopUrlDomain() .'index.php?Info&Content&' .'pages_id=' .   $Qpages->valueInt('pages_id') . '" target="_blank" rel="noreferrer">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview_catalog.png', $CLICSHOPPING_PageManager->getDef('icon_preview_catalog')) . '</a>'; ?></td>
<?php
      } else {
?>
              <td></td>
<?php
      }
?>
              <td><?php echo $Qpages->value('pages_title'); ?></td>
<?php
      if ($Qpages->valueInt('page_type') == 1){
        $page_function = $CLICSHOPPING_PageManager->getDef('page_manager_introduction_page');
      } elseif ($Qpages->valueInt('page_type') == 2) {
        $page_function = $CLICSHOPPING_PageManager->getDef('page_manager_main_page');
      } elseif ($Qpages->valueInt('page_type') == 3) {
        $page_function = $CLICSHOPPING_PageManager->getDef('page_manager_contact_us');
      } elseif ($Qpages->valueInt('page_type') == 5) {
        $page_function = $CLICSHOPPING_PageManager->getDef('page_manager_menu_header');
      } elseif ($Qpages->valueInt('page_type') == 6) {
        $page_function = $CLICSHOPPING_PageManager->getDef('page_manager_menu_footer');
      } else {
        $page_function = $CLICSHOPPING_PageManager->getDef('page_manager_informations');
      }
?>
              <td><?php echo $page_function; ?></td>
<?php
      if (MODE_B2B_B2C == 'true') {
?>
              <td><?php echo $customers_group['customers_group_name']; ?></td>
<?php
      }
?>
              <td class="text-md-center">
<?php
      if ($Qpages->valueInt('status') == 1) {
        echo '<a href="' . $CLICSHOPPING_PageManager->link('PageManager&SetFlag&flag=0&id=' .   $Qpages->valueInt('pages_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
      } else {
        echo '<a href="' . $CLICSHOPPING_PageManager->link('PageManager&SetFlag&flag=1&id=' .   $Qpages->valueInt('pages_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
      }
?>
              </td>
<?php
      if ($Qpages->value('links_target') == '_self') {
        $links_target =  $CLICSHOPPING_PageManager->getDef('text_link_same_windows');
      } elseif ($Qpages->value('links_target') == '_blank') {
        $links_target =  $CLICSHOPPING_PageManager->getDef('text_link_new_windows');
      } else {
        $links_target =  '';
      }
?>
              <td><?php echo $links_target; ?></td>
<?php
      if ($Qpages->valueInt('page_box') == 0) {
        $page_box =  $CLICSHOPPING_PageManager->getDef('page_manager_main_box');
      } elseif ($Qpages->valueInt('page_box') == 1) {
        $page_box =  $CLICSHOPPING_PageManager->getDef('page_manager_secondary_box');
      } elseif ($Qpages->valueInt('page_box') == 2) {
        $page_box =  $CLICSHOPPING_PageManager->getDef('page_manager_landing_page');
      } else {
        $page_box =  $CLICSHOPPING_PageManager->getDef('page_manager_none');
      }
?>
              <td><?php echo $page_box; ?></td>
              <td class="text-md-center"><?php echo $Qpages->valueInt('sort_order'); ?></td>
              <td class="text-md-right">
<?php
      echo '<a href="' . $CLICSHOPPING_PageManager->link('Edit&bID=' .   $Qpages->valueInt('pages_id') . '&page=' . $_GET['page']) .'">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_PageManager->getDef('icon_edit')) . '</a>';
      echo '&nbsp;';
      // suppression du bouton delete
      if ((  $Qpages->valueInt('pages_id') == 4) || (  $Qpages->valueInt('pages_id') == 5)) {
        echo '&nbsp;';
      } else {
        echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_PageManager->getDef('image_delete'));
      }
?>
                </td>
              </tbody>
            </tr>
<?php
    }
  } // end $listingTotalRow
?>
        </form><!-- end form delete all -->
      </table></td>
    </tr>
  </table>
<?php
  if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qpages->getPageSetLabel($CLICSHOPPING_PageManager->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qpages->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
  }
?>
</div>
