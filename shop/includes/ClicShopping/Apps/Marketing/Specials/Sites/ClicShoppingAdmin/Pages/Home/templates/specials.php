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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  $CLICSHOPPING_Specials = Registry::get('Specials');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');
  $CLICSHOPPING_Language = Registry::get('Language');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $languages = $CLICSHOPPING_Language->getLanguages();

  $customers_group = GroupsB2BAdmin::getAllGroups();
  $customers_group_name = '';

  foreach($customers_group as $value) {
    $customers_group_name .= '<option value="' . $value['id']  . '">' . $value['text']  . '</option>';
  } // end empty action
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/specials.gif', $CLICSHOPPING_Specials->getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Specials->getDef('heading_title'); ?></span>
           <span class="col-md-2">
           <div class="form-group">
             <div class="controls">
<?php
  if (MODE_B2B_B2C == 'true') {
    echo HTML::form('grouped', $CLICSHOPPING_Specials->link('Specials'), 'post', 'class="form-inline"');
    echo HTML::selectMenu('customers_group_id', GroupsB2BAdmin::getAllGroups(), $_POST['customers_group_id'], 'onchange="this.form.submit();"');
    echo '</form>';
  }
?>
             </div>
           </div>
         </span>
         <span class="col-md-3">
<?php
    if (MODE_B2B_B2C == 'true' && isset($_POST['customers_group_id'])) {
      echo HTML::button($CLICSHOPPING_Specials->getDef('button_reset'), null, $CLICSHOPPING_Specials->link('Specials'), 'warning');
    }
?>
         </span>
         <span class="col-md-4 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Specials->getDef('button_new'), null, $CLICSHOPPING_Specials->link('Edit&page=' . $page . '&action=new'), 'success');
  echo HTML::form('delete_all', $CLICSHOPPING_Specials->link('Specials&Specials&DeleteAll&page=' . $page));
?>
           <a onclick="$('delete').prop('action', ''); $('form').submit();" class="button"><span><?php echo HTML::button($CLICSHOPPING_Specials->getDef('button_delete'), null, null, 'danger'); ?></span></a>
         </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- //################################################################################################################ -->
  <!-- //                                             LISTING DES PROMOTIONS                                              -->
  <!-- //################################################################################################################ -->
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th width="1" class="text-md-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th><?php echo $CLICSHOPPING_Specials->getDef('table_heading_model'); ?></th>
          <th><?php echo $CLICSHOPPING_Specials->getDef('table_heading_products'); ?></th>
<?php
  // Permettre le changement de groupe en mode B2B
  if (MODE_B2B_B2C == 'true') {
?>
          <th><?php echo $CLICSHOPPING_Specials->getDef('table_heading_products_group'); ?></th>
<?php
  }
?>
          <th><?php echo $CLICSHOPPING_Specials->getDef('table_heading_percentage'); ?></td>
          <th><?php echo $CLICSHOPPING_Specials->getDef('table_heading_products_price'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Specials->getDef('table_heading_scheduled_date'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Specials->getDef('table_heading_expires_date'); ?></td>
           <th class="text-md-center"><?php echo $CLICSHOPPING_Specials->getDef('table_heading_flash_discount'); ?></td>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Specials->getDef('table_heading_archive'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Specials->getDef('table_heading_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Specials->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
<?php
  if (isset($_POST['customers_group_id']) && $_POST['customers_group_id'] != 0) {

    $customers_group_id = HTML::sanitize($_POST['customers_group_id']);

    $Qspecials = $CLICSHOPPING_Specials->db->prepare('select  SQL_CALC_FOUND_ROWS p.products_id,
                                                                                  p.products_model,
                                                                                  pd.products_name,
                                                                                  p.products_image,
                                                                                  p.products_price,
                                                                                  s.specials_id,
                                                                                  s.customers_group_id,
                                                                                  s.specials_new_products_price,
                                                                                  s.specials_date_added,
                                                                                  s.specials_last_modified,
                                                                                  s.scheduled_date,
                                                                                  s.expires_date,
                                                                                  s.date_status_change,
                                                                                  s.status,
                                                                                  p.products_archive,
                                                                                  s.flash_discount
                                                                 from :table_products p,
                                                                      :table_specials s,
                                                                      :table_products_description pd
                                                                where p.products_id = pd.products_id
                                                                and p.products_id = s.products_id
                                                                and pd.language_id = :language_id
                                                                and s.customers_group_id = :customers_group_id
                                                                order by pd.products_name
                                                                limit :page_set_offset, :page_set_max_results
                                                                ');

    $Qspecials->bindInt(':language_id', $CLICSHOPPING_Language->getId() );
    $Qspecials->bindInt(':customers_group_id', $customers_group_id);
    $Qspecials->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qspecials->execute();
  } else {

    $Qspecials = $CLICSHOPPING_Specials->db->prepare('select  SQL_CALC_FOUND_ROWS  p.products_id,
                                                                                  p.products_model,
                                                                                  pd.products_name,
                                                                                  p.products_image,
                                                                                  p.products_price,
                                                                                  s.specials_id,
                                                                                  s.customers_group_id,
                                                                                  s.specials_new_products_price,
                                                                                  s.specials_date_added,
                                                                                  s.specials_last_modified,
                                                                                  s.scheduled_date,
                                                                                  s.expires_date,
                                                                                  s.date_status_change,
                                                                                  s.status,
                                                                                  p.products_archive,
                                                                                  s.flash_discount
                                                               from :table_products p,
                                                                    :table_specials s,
                                                                    :table_products_description pd
                                                              where p.products_id = pd.products_id
                                                              and pd.language_id = :language_id
                                                              and p.products_id = s.products_id
                                                              order by pd.products_name
                                                              limit :page_set_offset, :page_set_max_results
                                                              ');

    $Qspecials->bindInt(':language_id', $CLICSHOPPING_Language->getId() );
    $Qspecials->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
    $Qspecials->execute();
  }

  $listingTotalRow = $Qspecials->getPageSetTotalRows();

  if ($listingTotalRow > 0) {

      while ($Qspecials->fetch()) {

      if ((!isset($_GET['sID']) || (isset($_GET['sID']) && ((int)$_GET['sID'] == $Qspecials->valueInt('specials_id')))) && !isset($sInfo)) {

          $Qproduct = $CLICSHOPPING_Db->get('products', 'products_image', ['products_id' => $Qspecials->valueInt('products_id')]);

        $sInfo_array = array_merge($Qspecials->toArray(), $Qproduct->toArray());
        $sInfo = new ObjectInfo($sInfo_array);
      }

      $QcustomersGroupPrice = $CLICSHOPPING_Specials->db->prepare('select customers_group_price
                                                                    from :table_products_groups
                                                                    where products_id = :products_id
                                                                    and customers_group_id =  :customers_group_id
                                                                  ');
      $QcustomersGroupPrice->bindInt(':products_id', $Qspecials->valueInt('products_id'));
      $QcustomersGroupPrice->bindInt(':customers_group_id', $sInfo->customers_group_id);

      $QcustomersGroupPrice->execute();

      if ($QcustomersGroupPrice->fetch()) {
        $price = $Qspecials->valueDecimal('products_price');
        $sInfo->products_price = $price = $QcustomersGroupPrice->valueDecimal('customers_group_price');
      }
?>
              <td>
<?php
      if (isset($_POST['selected'])) {
?>
                <input type="checkbox" name="selected[]" value="<?php echo $Qspecials->valueInt('specials_id'); ?>" checked="checked" />
<?php
      } else {
?>
                <input type="checkbox" name="selected[]" value="<?php echo $Qspecials->valueInt('specials_id'); ?>" />
<?php
      }
?>
              </td>
              <td scope="row" width="50px"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Preview&Preview&pID=' . $Qspecials->valueInt('products_id') . '?page=' . $page), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_Specials->getDef('icon_preview'))); ?></td>
              <td><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qspecials->value('products_image'), $Qspecials->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
              <td><?php echo $Qspecials->value('products_model'); ?></td>
              <td><?php echo $Qspecials->value('products_name'); ?></td>
<?php
      if (MODE_B2B_B2C == 'true') {
        if ($Qspecials->valueInt('customers_group_id') != 0 && $Qspecials->valueInt('customers_group_id') != 99) {
          $all_groups_name_special = GroupsB2BAdmin::getCustomersGroupName($Qspecials->valueInt('customers_group_id'));
        } elseif ($Qspecials->valueInt('customers_group_id') == 99) {
          $all_groups_name_special = $CLICSHOPPING_Specials->getDef('text_all_groups');
        } else {
          $all_groups_name_special = $CLICSHOPPING_Specials->getDef('visitor_name');
        }
?>
                <td><?php echo $all_groups_name_special; ?></td>
<?php
      } // end mode b2B_B2C
?>
                <td class="text-md-left"><?php echo number_format(100 - (($Qspecials->valueDecimal('specials_new_products_price') / $Qspecials->value('products_price')) * 100)) . '%'; ?></td>
                <td class="text-md-left"><span class="oldPrice"><?php echo $CLICSHOPPING_Currencies->format($Qspecials->valueDecimal('products_price')); ?></span><span class="specialPrice"><?php echo $CLICSHOPPING_Currencies->format($Qspecials->value('specials_new_products_price')); ?></span></td>
<?php
      if (!is_null($Qspecials->value('scheduled_date'))) {
?>
                <td class="text-md-center"><?php echo DateTime::toShort($Qspecials->value('scheduled_date')); ?></td>
<?php
      } else {
?>
                <td class="text-md-center"></td>
<?php
      }

      if (!is_null($Qspecials->value('expires_date'))) {
?>
                <td class="text-md-center"><?php echo DateTime::toShort($Qspecials->value('expires_date')); ?></td>
<?php
      } else {
?>
                <td class="text-md-center"></td>
<?php
      }
       if ( $Qspecials->valueInt('flash_discount') == 1) {
?>
                <td  class="text-md-center"><i class="fas fa-check fa-lg" aria-hidden="true"></i></td>
<?php
       } else {
?>
                <td></td>

<?php
       }
      if ( $Qspecials->valueInt('products_archive') == 1) {
?>
                <td class="text-md-center"><i class="fas fa-check fa-lg" aria-hidden="true"></i></td>
<?php
      } else {
?>
                <td></td>
<?php
      }
?>
                <td class="text-md-center">
<?php
      if ($Qspecials->valueInt('status') == 1) {
        echo '<a href="' . $CLICSHOPPING_Specials->link('Specials&Specials&SetFlag&page=' . (int)$page . '&flag=0&id=' . (int)$Qspecials->valueInt('specials_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
      } else {
        echo '<a href="' . $CLICSHOPPING_Specials->link('Specials&Specials&SetFlag&page=' . (int)$page . '&flag=1&id=' . (int)$Qspecials->valueInt('specials_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
      }
?>
                </td>
                <td class="text-md-right">
<?php
      echo '<a href="' . $CLICSHOPPING_Specials->link('Edit&page=' . (int)$page . '&sID=' . (int)$Qspecials->valueInt('specials_id') . '&action=update') . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Specials->getDef('icon_edit')) . '</a>' ;
      echo '&nbsp;';
?>
                </td>
              </tr>
<?php
    } // end while
  } // end $listingTotalRow
?>
            </tbody>
          </form><!-- end form delete all -->
        </tr>
      </table>
    </td></table>
<?php
      if ($listingTotalRow > 0) {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qspecials->getPageSetLabel($CLICSHOPPING_Specials->getDef('text_display_number_of_link')); ?></div>
        <div class="float-md-right text-md-right"><?php echo $Qspecials->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
      </div>
    </div>
<?php
    } // end $listingTotalRow
?>
</div>