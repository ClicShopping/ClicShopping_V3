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
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Currency = Registry::get('Currency');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Language = Registry::get('Language');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/currencies.gif', $CLICSHOPPING_Currency->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Currency->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Currency->getDef('button_insert'), null, $CLICSHOPPING_Currency->link('Insert'), 'success') . ' ';
  echo HTML::button($CLICSHOPPING_Currency->getDef('button_update_all'), null, $CLICSHOPPING_Currency->link('Currency&UpdateAll&page=' . $page), 'info');
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
          <th><?php echo $CLICSHOPPING_Currency->getDef('table_heading_currency_name'); ?></th>
          <th><?php echo $CLICSHOPPING_Currency->getDef('table_heading_currency_code'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Currency->getDef('table_heading_currency_value'); ?></th>
          <th
            class="text-md-center"><?php echo $CLICSHOPPING_Currency->getDef('table_heading_currency_last_updated'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Currency->getDef('table_heading_currency_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Currency->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
          $Qcurrency = $CLICSHOPPING_Currency->db->prepare('select  SQL_CALC_FOUND_ROWS currencies_id,
                                                                              title,
                                                                              code,
                                                                              symbol_left,
                                                                              symbol_right,
                                                                              decimal_point,
                                                                              thousands_point,
                                                                              decimal_places,
                                                                              last_updated,
                                                                              value,
                                                                              status
                                                from :table_currencies
                                                order by title
                                                limit :page_set_offset, :page_set_max_results
                                              ');

          $Qcurrency->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qcurrency->execute();

          $listingTotalRow = $Qcurrency->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qcurrency->fetch()) {
              if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] == $Qcurrency->valueInt('currencies_id')))) && !isset($cInfo)) {
                $cInfo = new ObjectInfo($Qcurrency->toArray());
              }

              if (DEFAULT_CURRENCY == $Qcurrency->value('code')) {
                echo '                <th scope="row"><strong>' . $Qcurrency->value('title') . ' (' . $CLICSHOPPING_Currency->getDef('text_default') . ')</strong></th>' . "\n";
              } else {
                echo '                <th scope="row">' . $Qcurrency->value('title') . '</th>' . "\n";
              }
              ?>
              <td><?php echo $Qcurrency->value('code'); ?></td>
              <td class="text-md-right"><?php echo number_format($Qcurrency->value('value'), 8); ?></td>
              <td class="text-md-center"><?php echo DateTime::toShort($Qcurrency->value('last_updated')); ?></td>
              <td class="text-md-center">
                <?php
                  if ($Qcurrency->valueInt('status') == 1) {
                    echo HTML::link($CLICSHOPPING_Currency->link('Currency&SetFlag&flag=0&cID=' . $Qcurrency->valueInt('currencies_id') . '&page=' . $page), '<i class="fas fa-check fa-lg" aria-hidden="true"></i>');
                  } else {
                    echo HTML::link($CLICSHOPPING_Currency->link('Currency&SetFlag&flag=1&cID=' . $Qcurrency->valueInt('currencies_id') . '&page=' . $page), '<i class="fas fa-times fa-lg" aria-hidden="true"></i>');
                  }
                ?>
              </td>
              <td class="text-md-right">
                <?php
                  echo HTML::link($CLICSHOPPING_Currency->link('Edit&page=' . $page . '&cID=' . $Qcurrency->valueInt('currencies_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Currency->getDef('icon_edit')));
                  echo '&nbsp;';
                  echo HTML::link($CLICSHOPPING_Currency->link('Delete&page=' . $page . '&cID=' . $Qcurrency->valueInt('currencies_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Currency->getDef('image_delete')));
                  echo '&nbsp;';
                ?>
              </td>
              </tr>
              <?php
            }
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
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qcurrency->getPageSetLabel($CLICSHOPPING_Currency->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $Qcurrency->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    }
  ?>
</div>