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

  $CLICSHOPPING_Langues = Registry::get('Langues');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/languages.gif', $CLICSHOPPING_Langues->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Langues->getDef('heading_title'); ?></span>
          <span
            class="col-md-7 text-md-right"><?php echo HTML::button($CLICSHOPPING_Langues->getDef('button_new'), null, $CLICSHOPPING_Langues->link('Insert&page=' . $page), 'success'); ?></span>
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
          <th><?php echo $CLICSHOPPING_Langues->getDef('table_heading_language_name'); ?></th>
          <th><?php echo $CLICSHOPPING_Langues->getDef('table_heading_language_code'); ?></th>
          <th class="text-md-center"><?php echo $CLICSHOPPING_Langues->getDef('table_heading_language_status'); ?></th>
          <th class="text-md-right"><?php echo $CLICSHOPPING_Langues->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
          $Qlanguages = $CLICSHOPPING_Langues->db->prepare('select  SQL_CALC_FOUND_ROWS  languages_id,
                                                                                   name,
                                                                                   code,
                                                                                   image,
                                                                                   directory,
                                                                                   sort_order,
                                                                                   status,
                                                                                   locale
                                                      from :table_languages
                                                      order by sort_order
                                                      limit :page_set_offset, :page_set_max_results
                                                      ');

          $Qlanguages->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qlanguages->execute();

          $listingTotalRow = $Qlanguages->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qlanguages->fetch()) {
              if ((!isset($_GET['lID']) || (isset($_GET['lID']) && ((int)$_GET['lID'] === $Qlanguages->valueInt('languages_id')))) && !isset($lInfo)) {
                $lInfo = new ObjectInfo($Qlanguages->toArray());
              }

              if (DEFAULT_LANGUAGE == $Qlanguages->value('code')) {
                echo '                <th scope="row"><strong>' . $Qlanguages->value('name') . ' (' . $CLICSHOPPING_Langues->getDef('text_default') . ')</strong></th>' . "\n";
              } else {
                echo '                <th scope="row">' . $Qlanguages->value('name') . '</th>' . "\n";
              }
              ?>
              <th scope="row"><?php echo $Qlanguages->value('code'); ?></td>
              <td class="text-md-center">
                <?php
                  //pb when the english when the status is off
                  //      if ($Qlanguages->valueInt('languages_id') != 1 && DEFAULT_LANGUAGE != $Qlanguages->value('code')) {
                  if ($Qlanguages->valueInt('status') == 1) {
                    echo HTML::link($CLICSHOPPING_Langues->link('Langues&SetFlag&flag=0&page=' . $page . '&lid=' . $Qlanguages->valueInt('languages_id')), '<i class="fas fa-check fa-lg" aria-hidden="true"></i>');
                  } else {
                    echo HTML::link($CLICSHOPPING_Langues->link('Langues&SetFlag&flag=1&page=' . $page . '&lid=' . $Qlanguages->valueInt('languages_id')), '<i class="fas fa-times fa-lg" aria-hidden="true"></i>');
                  }
                  //      }
                ?>
              </td>
              <td class="text-md-right">
                <?php
                  echo HTML::link($CLICSHOPPING_Langues->link('Edit&page=' . $page . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=edit'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Langues->getDef('icon_edit')));
                  echo '&nbsp;';

                  if ($Qlanguages->valueInt('languages_id') > 1) {
                    echo HTML::link($CLICSHOPPING_Langues->link('Delete&page=' . $page . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=delete'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Langues->getDef('image_delete')));
                  }
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
    </td>
  </table>

  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qlanguages->getPageSetLabel($CLICSHOPPING_Langues->getDef('text_display_number_of_link')); ?></div>
          <div class="float-md-right text-md-right"> <?php echo $Qlanguages->getPageSetLinks(); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>
