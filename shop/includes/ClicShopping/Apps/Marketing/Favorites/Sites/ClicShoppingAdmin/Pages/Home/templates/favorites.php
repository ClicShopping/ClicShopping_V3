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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  $CLICSHOPPING_Favorites = Registry::get('Favorites');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');
  $CLICSHOPPING_Language = Registry::get('Language');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $languages = $CLICSHOPPING_Language->getLanguages();

  $customers_group = GroupsB2BAdmin::getAllGroups();
  $customers_group_name = '';

  foreach ($customers_group as $value) {
    $customers_group_name .= '<option value="' . $value['id'] . '">' . $value['text'] . '</option>';
  } // end empty action
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/products_favorites.png', $CLICSHOPPING_Favorites->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Favorites->getDef('heading_title'); ?></span>
          <span class="col-md-2">
           <div class="form-group">
             <div class="controls">
<?php
  if (MODE_B2B_B2C == 'true') {

    if (isset($_POST['customers_group_id'])) {
      $customers_group_id = $_POST['customers_group_id'];
    } else {
      $customers_group_id = null;
    }

    echo HTML::form('grouped', $CLICSHOPPING_Favorites->link('Favorites'), 'post', 'class="form-inline"');
    echo HTML::selectMenu('customers_group_id', GroupsB2BAdmin::getAllGroups(), $customers_group_id, 'onchange="this.form.submit();"');
    echo '</form>';
  }
?>
             </div>
           </div>
         </span>
          <span class="col-md-3">
<?php
  if (MODE_B2B_B2C == 'true' && isset($_POST['customers_group_id'])) {
    echo HTML::button($CLICSHOPPING_Favorites->getDef('button_reset'), null, $CLICSHOPPING_Favorites->link('Favorites'), 'warning');
  }
?>
         </span>
          <span class="col-md-4 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Favorites->getDef('button_new'), null, $CLICSHOPPING_Favorites->link('Edit&page=' . $page . '&action=new'), 'success');
  echo HTML::form('delete_all', $CLICSHOPPING_Favorites->link('Favorites&Favorites&DeleteAll&page=' . $page));
?>
           <a onclick="$('delete').prop('action', ''); $('form').submit();"
              class="button"><span><?php echo HTML::button($CLICSHOPPING_Favorites->getDef('button_delete'), null, null, 'danger'); ?></span></a>
         </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- //################################################################################################################ -->
  <!-- //                                             LISTING DES COUPS DE COEUR                                             -->
  <!-- //################################################################################################################ -->
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th width="1" class="text-md-center"><input type="checkbox"
                                                      onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
          </th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_model'); ?></th>
          <th><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_products'); ?></th>
          <?php
            // Permettre le changement de groupe en mode B2B
            if (MODE_B2B_B2C == 'true') {
              ?>
              <th><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_products_group'); ?></th>
              <?php
            }
          ?>
          <th><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_products_price'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_scheduled_date'); ?></th>
          <th class="text-md-center">
          <?php echo $CLICSHOPPING_Favorites->getDef('table_heading_expires_date'); ?></td>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_archive'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Favorites->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
          if (isset($_POST['customers_group_id'])) {

            $customers_group_id = (int)$_POST['customers_group_id'];

            $Qfavorites = $CLICSHOPPING_Favorites->db->prepare('select  SQL_CALC_FOUND_ROWS p.products_id,
                                                                                    p.products_model,
                                                                                    p.products_image,
                                                                                    pd.products_name,
                                                                                    p.products_price,
                                                                                    s.products_favorites_id,
                                                                                    s.customers_group_id,
                                                                                    s.products_favorites_date_added,
                                                                                    s.products_favorites_last_modified,
                                                                                    s.scheduled_date,
                                                                                    s.expires_date,
                                                                                    s.date_status_change,
                                                                                    s.status,
                                                                                    p.products_archive
                                                       from :table_products p,
                                                            :table_products_favorites s,
                                                            :table_products_description pd
                                                      where p.products_id = pd.products_id
                                                      and pd.language_id = :language_id
                                                      and p.products_id = s.products_id
                                                      and s.customers_group_id = :customers_group_id
                                                      order by pd.products_name
                                                      limit :page_set_offset, :page_set_max_results
                                                    ');

            $Qfavorites->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $Qfavorites->bindInt(':customers_group_id', $customers_group_id);
            $Qfavorites->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
            $Qfavorites->execute();
          } else {
            $Qfavorites = $CLICSHOPPING_Favorites->db->prepare('select SQL_CALC_FOUND_ROWS p.products_id,
                                                                                  p.products_model,
                                                                                  p.products_image,
                                                                                  pd.products_name,
                                                                                  p.products_price,
                                                                                  s.products_favorites_id,
                                                                                  s.customers_group_id,
                                                                                  s.products_favorites_date_added,
                                                                                  s.products_favorites_last_modified,
                                                                                  s.scheduled_date,
                                                                                  s.expires_date,
                                                                                  s.date_status_change,
                                                                                  s.status,
                                                                                  p.products_archive
                                                       from :table_products p,
                                                            :table_products_favorites s,
                                                            :table_products_description pd
                                                      where p.products_id = pd.products_id
                                                      and pd.language_id = :language_id
                                                      and p.products_id = s.products_id
                                                      order by pd.products_name
                                                      limit :page_set_offset, :page_set_max_results
                                                      ');

            $Qfavorites->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $Qfavorites->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
            $Qfavorites->execute();
          }

          $listingTotalRow = $Qfavorites->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qfavorites->fetch()) {

              if ((!isset($_GET['sID']) || (isset($_GET['sID']) && ($_GET['sID'] == $Qfavorites->valueInt('products_favorites_id')))) && !isset($sInfo)) {

                $Qproduct = $CLICSHOPPING_Db->get('products', 'products_image', ['products_id' => $Qfavorites->valueInt('products_id')]);

                $sInfo_array = array_merge($Qfavorites->toArray(), $Qproduct->toArray());
                $sInfo = new ObjectInfo($sInfo_array);
              }
              ?>
              <td>
                <?php
                  if (isset($_POST['selected'])) {
                    ?>
                    <input type="checkbox" name="selected[]"
                           value="<?php echo $Qfavorites->valueInt('products_favorites_id'); ?>" checked="checked"/>
                    <?php
                  } else {
                    ?>
                    <input type="checkbox" name="selected[]"
                           value="<?php echo $Qfavorites->valueInt('products_favorites_id'); ?>"/>
                    <?php
                  }
                ?>
              </td>
              <td scope="row"
                  width="50px"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Preview&Preview&pID=' . $Qfavorites->valueInt('products_id') . '?page=' . $page), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_Favorites->getDef('icon_preview'))); ?></td>
              <td><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qfavorites->value('products_image'), $Qfavorites->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
              <td><?php echo $Qfavorites->value('products_model'); ?></td>
              <td><?php echo $Qfavorites->value('products_name'); ?></td>
              <?php
              if (MODE_B2B_B2C == 'true') {
                if ($Qfavorites->valueInt('customers_group_id') != 0 && $Qfavorites->valueInt('customers_group_id') != 99) {
                  $all_groups_name_products_favorites = GroupsB2BAdmin::getCustomersGroupName($Qfavorites->valueInt('customers_group_id'));
                } elseif ($Qfavorites->valueInt('customers_group_id') == 99) {
                  $all_groups_name_products_favorites = $CLICSHOPPING_Favorites->getDef('text_all_groups');
                } else {
                  $all_groups_name_products_favorites = $CLICSHOPPING_Favorites->getDef('visitor_name');
                }
                ?>
                <td><?php echo $all_groups_name_products_favorites; ?></td>
                <?php
              } // end mode b2B_B2C
              ?>
              <td
                class="text-md-left"><?php echo $CLICSHOPPING_Currencies->format($Qfavorites->value('products_price')); ?></td>
              <?php
              if (!is_null($Qfavorites->value('scheduled_date'))) {
                ?>
                <td class="text-md-center"><?php echo DateTime::toShort($Qfavorites->value('scheduled_date')); ?></td>
                <?php
              } else {
                ?>
                <td class="text-md-center"></td>
                <?php
              }

              if (!is_null($Qfavorites->value('expires_date'))) {
                ?>
                <td class="text-md-center"><?php echo DateTime::toShort($Qfavorites->value('expires_date')); ?></td>
                <?php
              } else {
                ?>
                <td class="text-md-center"></td>
                <?php
              }

              if ($Qfavorites->valueInt('products_archive') == 1) {
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
                  if ($Qfavorites->valueInt('status') == 1) {
                    echo '<a href="' . $CLICSHOPPING_Favorites->link('Favorites&Favorites&SetFlag&page=' . (int)$page . '&flag=0&id=' . (int)$Qfavorites->valueInt('products_favorites_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                  } else {
                    echo '<a href="' . $CLICSHOPPING_Favorites->link('Favorites&Favorites&SetFlag&page=' . (int)$page . '&flag=1&id=' . (int)$Qfavorites->valueInt('products_favorites_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                  }
                ?>
              </td>
              <td class="text-md-right">
                <?php
                  echo '<a href="' . $CLICSHOPPING_Favorites->link('Edit&page=' . (int)$page . '&sID=' . (int)$Qfavorites->valueInt('products_favorites_id') . '&action=update') . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Favorites->getDef('icon_edit')) . '</a>';
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
    </td>
  </table>
  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qfavorites->getPageSetLabel($CLICSHOPPING_Favorites->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"> <?php echo $Qfavorites->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>