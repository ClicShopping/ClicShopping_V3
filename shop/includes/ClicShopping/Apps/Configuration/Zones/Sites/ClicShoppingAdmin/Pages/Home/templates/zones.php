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

  $CLICSHOPPING_Zones = Registry::get('Zones');
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
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/zones.gif', $CLICSHOPPING_Zones->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-3 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Zones->getDef('heading_title'); ?></span>
          <span class="col-md-3">
           <div class="form-group">
             <div class="controls">
<?php
  echo HTML::form('search', $CLICSHOPPING_Zones->link('Zones'), 'post', null, ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Zones->getDef('heading_title_search') . '"');

  if (isset($_POST['search'])) {
    echo HTML::button($CLICSHOPPING_Zones->getDef('button_reset'), null, $CLICSHOPPING_Zones->link('Zones'), 'warning') . '&nbsp;';
    $search = HTML::sanitize($_POST['search']);
  } elseif(isset($_GETT['search'])) {
    echo HTML::button($CLICSHOPPING_Zones->getDef('button_reset'), null, $CLICSHOPPING_Zones->link('Zones'), 'warning') . '&nbsp;';
    $search = HTML::sanitize($_GET['search']);
  } else {
    $search = '';
  }
?>
               </form>
             </div>
            </div>
          </span>
          <span class="col-md-5 text-md-right">
            <?php echo HTML::button($CLICSHOPPING_Zones->getDef('button_new'), null, $CLICSHOPPING_Zones->link('Insert&page=' . $page), 'success'); ?>
            <?php echo HTML::form('flag_all', $CLICSHOPPING_Zones->link('Zones&AllFlag', 'page=' . $page)); ?>
            <a onclick="$('flag_all').prop('action', ''); $('form').submit();"
               class="button"><?php echo HTML::button($CLICSHOPPING_Zones->getDef('button_status'), null, null, 'primary'); ?></a>&nbsp;
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
          <th width="1" class="text-md-center"><input type="checkbox"
                                                      onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
          </th>
          <th><?php echo $CLICSHOPPING_Zones->getDef('table_heading_country_name'); ?></th>
          <th><?php echo $CLICSHOPPING_Zones->getDef('table_heading_zone_name'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Zones->getDef('table_heading_zone_code'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Zones->getDef('table_heading_zone_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Zones->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
          if (isset($search)) {
            $Qzones = $CLICSHOPPING_Zones->db->prepare('select  SQL_CALC_FOUND_ROWS  z.zone_id,
                                                                                    c.countries_id,
                                                                                    c.countries_name,
                                                                                    z.zone_name,
                                                                                    z.zone_code,
                                                                                    z.zone_country_id,
                                                                                    z.zone_status
                                                          from :table_zones z,
                                                               :table_countries c
                                                          where z.zone_country_id = c.countries_id
                                                          and c.countries_name like :search
                                                          order by c.countries_name,
                                                                   z.zone_name
                                                          limit :page_set_offset,
                                                                :page_set_max_results
                                                          ');

            $Qzones->bindValue(':search', '%' . $search . '%');
            $Qzones->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
            $Qzones->execute();
          } else {
            $Qzones = $CLICSHOPPING_Zones->db->prepare('select  SQL_CALC_FOUND_ROWS  z.zone_id,
                                                                                    c.countries_id,
                                                                                    c.countries_name,
                                                                                    z.zone_name,
                                                                                    z.zone_code,
                                                                                    z.zone_country_id,
                                                                                    z.zone_status
                                                          from :table_zones z,
                                                               :table_countries c
                                                          where z.zone_country_id = c.countries_id
                                                          order by c.countries_name,
                                                                   z.zone_name
                                                          limit :page_set_offset,
                                                                :page_set_max_results
                                                          ');
            $Qzones->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
            $Qzones->execute();
          }

          $listingTotalRow = $Qzones->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

          while ($Qzones->fetch()) {
            if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] === $Qzones->valueInt('zone_id')))) && !isset($cInfo)) {
              $cInfo = new ObjectInfo($Qzones->toArray());
            }
            ?>
            <th>
              <?php
                if (isset($_POST['selected'])) {
                  ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $Qzones->valueInt('zone_id'); ?>"
                         checked="checked" /><?php HTML::hiddenField('flag_selected', $Qzones->valueInt('zone_status')); ?>
                  <?php
                } else {
                  ?>
                  <input type="checkbox" name="selected[]"
                         value="<?php echo $Qzones->valueInt('zone_id'); ?>" /><?php HTML::hiddenField('flag_selected', $Qzones->valueInt('zone_status')); ?>
                  <?php
                }
              ?>
            </th>
            <th scope="row"><?php echo $Qzones->value('countries_name'); ?></th>
            <td><?php echo $Qzones->value('zone_name'); ?></td>
            <td><?php echo $Qzones->value('zone_code'); ?></td>
            <td class="text-md-center">
              <?php
                if ($Qzones->valueInt('zone_status') == 0) {
                  echo '<a href="' . $CLICSHOPPING_Zones->link('Zones&SetFlag&page=' . $page . '&flag=1&id=' . $Qzones->valueInt('zone_id') . '&search=' . $search) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                } else {
                  echo '<a href="' . $CLICSHOPPING_Zones->link('Zones&SetFlag&page=' . $page . '&flag=0&id=' . $Qzones->valueInt('zone_id')) . '&search=' . $search . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                }
              ?>
            </td>
            <td class="text-md-right">
              <?php

                echo '<a href="' . $CLICSHOPPING_Zones->link('Edit&page=' . $page . '&cID=' . $Qzones->valueInt('zone_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Zones->getDef('icon_edit')) . '</a>';
                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_Zones->link('Delete&&page=' . $page . '&cID=' . $Qzones->valueInt('zone_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Zones->getDef('icon_delete')) . '</a>';
                echo '&nbsp;';
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
  </table>
  </td>
  </table>
  </form>


  <div class="row">
    <div class="col-md-12">
      <div
        class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qzones->getPageSetLabel($CLICSHOPPING_Zones->getDef('text_display_number_of_link')); ?></div>
      <div
        class="float-md-right text-md-right"><?php echo $Qzones->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
    </div>
  </div>
</div>
