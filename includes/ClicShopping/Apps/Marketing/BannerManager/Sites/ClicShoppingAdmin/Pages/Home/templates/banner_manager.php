<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  $CLICSHOPPING_BannerManager = Registry::get('BannerManager');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Language = Registry::get('Language');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

  $action = $_GET['action'] ?? '';

  $languages = $CLICSHOPPING_Language->getLanguages();

  echo HTMLOverrideAdmin::getCkeditor();
?>

<div class="contentBody">

  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/banner_manager.gif', $CLICSHOPPING_BannerManager->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_BannerManager->getDef('heading_title'); ?></span>
          <span class="col-md-2">
            <?php echo HTML::form('search', $CLICSHOPPING_BannerManager->link('BannerManager'), 'post', null, ['session_id' => true]); ?>
            <?php echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_BannerManager->getDef('heading_title_search') . '"'); ?>
            </form>
          </span>
<?php
  if (isset($_POST['search']) && !\is_null($_POST['search'])) {
    ?>
          <span class="col-md-1"><?php echo HTML::button($CLICSHOPPING_BannerManager->getDef('button_reset'), null, $CLICSHOPPING_BannerManager->link('BannerManager'), 'warning'); ?></span>
    <?php
  }
?>
          <span
            class="col-md-3 text-end"><?php echo HTML::button($CLICSHOPPING_BannerManager->getDef('button_new_banner'), null, $CLICSHOPPING_BannerManager->link('Insert'), 'success'); ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="separator"></div>

  <table
    id="table"
    data-toggle="table"
    data-icons-prefix="bi"
    data-icons="icons"
    data-toolbar="#toolbar"
    data-sort-name="groupe"
    data-sort-order="asc"
    data-buttons-class="primary"
    data-show-toggle="true"
    data-show-columns="true"
    data-mobile-responsive="true">

    <thead class="dataTableHeadingRow">
    <tr>
      <th data-field="admin"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_banners_admin'); ?></th>
      <th data-field="banners"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_banners'); ?></th>
      <th data-field="groupe" data-sortable="true"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_groups'); ?></th>
      <th data-field="statistics" data-sortable="true"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_statistics'); ?></th>
      <th data-field="status" data-sortable="true"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_status'); ?></th>
      <?php
        // Permettre l'affichage des groupes en mode B2B
        if (MODE_B2B_B2C == 'true') {
          ?>
          <th data-field="customers_group" data-sortable="true"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_customers_group'); ?></th>
          <?php
        }
      ?>
      <th data-field="language" data-sortable="true"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_language'); ?></th>
      <th data-field="action" data-switchable="false" class="text-end"><?php echo $CLICSHOPPING_BannerManager->getDef('table_heading_action'); ?>&nbsp;
      </th>
    </tr>
    </thead>
    <tbody>
    <?php
      $search = '';

      if (isset($_POST['search']) && !\is_null($_POST['search'])) {
        $keywords = HTML::sanitize($_POST['search']);

        $search = " (banners_title like '%" . $keywords . "%'
              or banners_title_admin like '%" . $keywords . "%'
              or banners_group like '%" . $keywords . "%'
             )
          ";

        $Qbanner = $CLICSHOPPING_BannerManager->db->prepare('select SQL_CALC_FOUND_ROWS banners_id,
                                                                                       banners_title,
                                                                                       banners_image,
                                                                                       banners_group,
                                                                                       banners_target,
                                                                                       status,
                                                                                       expires_date,
                                                                                       expires_impressions,
                                                                                       date_status_change,
                                                                                       date_scheduled,
                                                                                       date_added,
                                                                                       customers_group_id,
                                                                                       languages_id,
                                                                                       banners_title_admin
                                                           from :table_banners
                                                           where ' . $search . '
                                                           order by banners_title_admin desc,
                                                                    banners_title,
                                                                    banners_group
                                                           limit :page_set_offset, :page_set_max_results
                                                        ');

      } else {
        $Qbanner = $CLICSHOPPING_BannerManager->db->prepare('select SQL_CALC_FOUND_ROWS banners_id,
                                                                                       banners_title,
                                                                                       banners_image,
                                                                                       banners_group,
                                                                                       banners_target,
                                                                                       status,
                                                                                       expires_date,
                                                                                       expires_impressions,
                                                                                       date_status_change,
                                                                                       date_scheduled,
                                                                                       date_added,
                                                                                       customers_group_id,
                                                                                       languages_id,
                                                                                       banners_title_admin
                                                             from :table_banners
                                                             order by banners_title_admin desc,
                                                                      banners_title,
                                                                      banners_group
                                                             limit :page_set_offset, :page_set_max_results
                                                        ');
      }

      $Qbanner->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $Qbanner->execute();

      $listingTotalRow = $Qbanner->getPageSetTotalRows();

      if ($listingTotalRow > 0) {
        while ($Qbanner->fetch()) {
          $Qinfo = $CLICSHOPPING_BannerManager->db->prepare('select sum(banners_shown) as banners_shown,
                                                                     sum(banners_clicked) as banners_clicked
                                                               from :table_banners_history
                                                               where banners_id = :banners_id
                                                             ');
          $Qinfo->bindInt(':banners_id', $Qbanner->valueInt('banners_id'));
          $Qinfo->execute();

// Permettre l'affichage des groupes en mode B2B
          if (MODE_B2B_B2C == 'true') {
            $QcustomersGroup = $CLICSHOPPING_BannerManager->db->prepare('select customers_group_name
                                                                          from :table_customers_groups
                                                                          where customers_group_id = :customers_group_id
                                                                        ');
            $QcustomersGroup->bindInt(':customers_group_id', $Qbanner->valueInt('customers_group_id'));
            $QcustomersGroup->execute();

            $customers_group = $QcustomersGroup->fetch();

            if ($Qbanner->valueInt('customers_group_id') == 99) {
              $customers_group['customers_group_name'] = $CLICSHOPPING_BannerManager->getDef('text_all_groups');
            } elseif ($Qbanner->valueInt('customers_group_id') == 0) {
              $customers_group['customers_group_name'] = $CLICSHOPPING_BannerManager->getDef('normal_customer');
            }
          }

          if ($Qbanner->valueInt('languages_id') != 0) {
            $QbannerLanguages = $CLICSHOPPING_BannerManager->db->prepare('select name
                                                                         from :table_languages
                                                                         where languages_id = :languages_id
                                                                        ');
            $QbannerLanguages->bindInt(':languages_id', $Qbanner->valueInt('languages_id'));
            $QbannerLanguages->execute();

            $banner_language = $QbannerLanguages->fetch();
          } else {
            $banner_language['name'] = $CLICSHOPPING_BannerManager->getDef('text_all_languages');
          }

          if (!isset($_GET['bID']) || ((isset($_GET['bID'])) && (int)$_GET['bID']) == $Qbanner->valueInt('banners_id')) {
            $bInfo_array = array_merge($Qbanner->toArray(), $Qinfo->toArray());
            $bInfo = new ObjectInfo($bInfo_array);
          }

          $banners_shown = (!\is_null($Qinfo->valueInt('banners_shown'))) ? $Qinfo->valueInt('banners_shown') : '0';
          $banners_clicked = (!\is_null($Qinfo->valueInt('banners_clicked'))) ? $Qinfo->valueInt('banners_clicked') : '0';
          ?>
          <tr>
            <td scope="row"><?php echo $Qbanner->value('banners_title_admin'); ?></td>
            <td><?php echo $Qbanner->value('banners_title'); ?></td>
            <td><?php echo $Qbanner->value('banners_group'); ?></td>
            <td><?php echo $banners_shown . ' / ' . $banners_clicked; ?></td>
            <td>
              <?php
                if ($Qbanner->valueInt('status') == 1) {
                  echo '<a href="' . $CLICSHOPPING_BannerManager->link('BannerManager&SetFlag&page=' . $page . '&bID=' . $Qbanner->valueInt('banners_id') . '&flag=0') . '"><i class="bi-check text-success"></i></a>';
                } else {
                  echo '<a href="' . $CLICSHOPPING_BannerManager->link('BannerManager&SetFlag&page=' . $page . '&bID=' . $Qbanner->valueInt('banners_id') . '&flag=1') . '"><i class="bi bi-x text-danger"></i></a>';
                }
              ?>
            </td>
            <?php
              if (MODE_B2B_B2C == 'true') {
                ?>
                <td><?php echo $customers_group['customers_group_name']; ?></td>
                <?php
              }
            ?>
            <td><?php echo $banner_language['name']; ?></td>
            <td class="text-end">
              <?php
                echo '<a href="' . $CLICSHOPPING_BannerManager->link('Update&page=' . $page . '&bID=' . $Qbanner->valueInt('banners_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_BannerManager->getDef('icon_edit')) . '</a>';
                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_BannerManager->link('BannerManager&CopyTo&page=' . $page . '&bID=' . $Qbanner->valueInt('banners_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/copy.gif', $CLICSHOPPING_BannerManager->getDef('icon_copy_to')) . '</a>';
                echo '&nbsp;';
                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_BannerManager->link('Delete&page=' . $page . '&bID=' . $Qbanner->valueInt('banners_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_BannerManager->getDef('icon_delete')) . '</a>';
                echo '&nbsp;';
              ?>
            </td>
          </tr>
          <?php
        }
      } // end $listingTotalRow
    ?>
    </tbody>
  </table>

  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-start pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qbanner->getPageSetLabel($CLICSHOPPING_BannerManager->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-end text-end"><?php echo $Qbanner->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>


