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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Apps;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_Groups = Registry::get('Groups');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
/*
  if ($CLICSHOPPING_MessageStack->exists('groups')) {
    echo $CLICSHOPPING_MessageStack->get('groups');
  }
*/
  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  // Permettre l'utilisation de des groupes clients
  if (MODE_B2B_B2C == 'false')  CLICSHOPPING::redirect();

?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">

          <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/group_client.gif', $CLICSHOPPING_Groups->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-7 pageHeading" width="60%"><?php echo '&nbsp;' . $CLICSHOPPING_Groups->getDef('heading_title'); ?></span>
          <span class="col-md-2 text-md-right">
            <div class="form-group">
              <div class="controls">
<?php
  echo HTML::form('search', 'Groups', 'post', null, ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Groups->getDef('heading_title_search') . '"');
?>
                </form>
              </div>
            </div>
          </span>
          <span class="col-md-2 text-md-right">
            <span class="col-md-6">
              <?php echo HTML::button($CLICSHOPPING_Groups->getDef('button_insert'), null, $CLICSHOPPING_Groups->link('Insert'), 'success'); ?>
            </span>
<?php
  if (!is_null($_GET['search'])) {
?>
    <span class="col-md-6"> <?php echo HTML::button($CLICSHOPPING_Groups->getDef('button_reset'), null, $CLICSHOPPING_Groups->link('customers_groups.php', null), 'warning'); ?></span>
    <?php
  }
?>
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
          <th><?php echo $CLICSHOPPING_Groups->getDef('table_heading_name'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Groups->getDef('table_heading_color'); ?></th>
          <th><?php echo $CLICSHOPPING_Groups->getDef('table_heading_quantity_default'); ?></th>
<?php
  if (B2B == 'true'){
?>
    <th><?php echo $CLICSHOPPING_Groups->getDef('table_heading_discount_b2b'); ?></th>
<?php
  } else {
?>
    <th><?php echo $CLICSHOPPING_Groups->getDef('table_heading_discount'); ?></th>
<?php
  }
?>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Groups->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
<?php
  $search = '';
  if ( ($_POST['search']) && (!is_null($_POST['search'])) ) {
    $keywords = HTML::sanitize($_POST['search']);

    $QustomersGroup = $CLICSHOPPING_Groups->db->prepare('select  SQL_CALC_FOUND_ROWS *
                                                         from :table_customers_groups
                                                         where customers_group_name like :search
                                                         limit :page_set_offset,
                                                              :page_set_max_results
                                                        ');
    $QustomersGroup->bindvalue(':search', '%' . $keywords . '%');
    $QustomersGroup->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $QustomersGroup->execute();

  } else {

    $QustomersGroup = $CLICSHOPPING_Groups->db->prepare('select  SQL_CALC_FOUND_ROWS *
                                                         from :table_customers_groups
                                                         limit :page_set_offset,
                                                              :page_set_max_results
                                                        ');
    $QustomersGroup->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $QustomersGroup->execute();
  }

  $listingTotalRow = $QustomersGroup->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

    while ($customers_group = $QustomersGroup->fetch()) {
      if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] == $QustomersGroup->valueInt('customers_group_id')))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
        $cInfo = new ObjectInfo($QustomersGroup->toArray());
      }
?>
              <th scope="row"><?php echo $QustomersGroup->value('customers_group_name'); ?></th>
              <td class="text-md-center">
                <table cellspacing="0" cellpadding="0" border="0"  width="30px">
                  <td class="text-md-center" bgcolor="<?php echo $QustomersGroup->value('color_bar'); ?>">&nbsp;</td>
                </table>
              </td>
              <td class="text-md-left"><?php echo $QustomersGroup->valueInt('customers_group_quantity_default'); ?></td>
              <td class="text-md-left"><?php echo $QustomersGroup->value('customers_group_discount'); ?>%</td>
              <td class="text-md-right">
<?php
      echo HTML::link($CLICSHOPPING_Groups->link('Edit&page=' . $_GET['page'] . '&cID=' . $QustomersGroup->valueInt('customers_group_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Groups->getDef('icon_edit')));
      echo '&nbsp;';
        echo HTML::link($CLICSHOPPING_Groups->link('Groups&UpdateAllPrice&page=' . $_GET['page'] . '&cID=' . $QustomersGroup->valueInt('customers_group_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/actualiser.gif', $CLICSHOPPING_Groups->getDef('icon_update')));
      echo '&nbsp;';

      if ($QustomersGroup->valueInt('customers_group_id') > 1) {
        echo  HTML::link($CLICSHOPPING_Groups->link('Groups&Delete&cID=' . $QustomersGroup->valueInt('customers_group_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Groups->getDef('image_delete')));
        echo '&nbsp;';
      }
?>
              </td>
              </tr>
<?php
    }
  } // end $listingTotalRow
?>
      </table>
    </td>
    </tr>
  </table>
<?php
  if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $QustomersGroup->getPageSetLabel($CLICSHOPPING_Groups->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $QustomersGroup->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
  } // end $listingTotalRow
?>
</div>

