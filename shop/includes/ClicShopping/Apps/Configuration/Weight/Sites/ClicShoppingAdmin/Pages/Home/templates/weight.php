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

  use ClicShopping\Apps\Configuration\Weight\Classes\ClicShoppingAdmin\WeightAdmin;

  $CLICSHOPPING_Weight = Registry::get('Weight');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/weight.png', $CLICSHOPPING_Weight->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Weight->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Weight->getDef('button_insert_weight'), null, $CLICSHOPPING_Weight->link('WeightInsert&page=' . $page), 'primary') . ' ';
  echo HTML::button($CLICSHOPPING_Weight->getDef('button_insert_class'), null, $CLICSHOPPING_Weight->link('ClassInsert&page=' . $page), 'success');
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
          <td><?php echo $CLICSHOPPING_Weight->getDef('table_heading_weight_class_id'); ?></td>
          <td><?php echo $CLICSHOPPING_Weight->getDef('table_heading_weight_class_symbol'); ?></td>
          <td><?php echo $CLICSHOPPING_Weight->getDef('table_heading_weight_class_type'); ?></td>
          <td><?php echo $CLICSHOPPING_Weight->getDef('table_heading_weight_class_to_id'); ?></td>
          <td><?php echo $CLICSHOPPING_Weight->getDef('table_heading_weight_class_rule'); ?></td>
          <td class="text-md-right"><?php echo $CLICSHOPPING_Weight->getDef('table_heading_action'); ?>&nbsp;</td>
        </tr>
        </thead>
        <tbody>
        <?php

          $Qweight = $CLICSHOPPING_Weight->db->prepare('select SQL_CALC_FOUND_ROWS  wc.weight_class_id,
                                                                             wc.weight_class_key,
                                                                             wc.language_id,
                                                                             wc.weight_class_title,
                                                                             tc.weight_class_from_id,
                                                                             tc.weight_class_to_id,
                                                                             tc.weight_class_rule
                                              from :table_weight_classes wc,
                                                   :table_weight_classes_rules tc 
                                              where wc.weight_class_id = tc.weight_class_from_id
                                              and wc.language_id = :language_id
                                              limit :page_set_offset,
                                                    :page_set_max_results
                                              ');
          $Qweight->bindInt(':language_id', $CLICSHOPPING_Language->getID());
          $Qweight->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
          $Qweight->execute();

          $listingTotalRow = $Qweight->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            while ($Qweight->fetch()) {
              if ((!isset($_GET['wID']) || (isset($_GET['wID']) && ((int)$_GET['wID'] === $Qweight->valueInt('weight_id')))) && !isset($trInfo)) {
                $trInfo = new ObjectInfo($Qweight->toArray());
              }

              $weight_class_title = WeightAdmin::getTitle($Qweight->valueInt('weight_class_to_id'), $CLICSHOPPING_Language->getID());

              ?>
              <th scope="row"><?php echo $Qweight->valueInt('weight_class_id'); ?></th>
              <td><?php echo $Qweight->value('weight_class_key'); ?></td>
              <td><?php echo $Qweight->value('weight_class_title'); ?></td>
              <td><?php echo $weight_class_title; ?></td>
              <td><?php echo $Qweight->value('weight_class_rule'); ?></td>
              <td class="text-md-right">
                <?php
                  echo HTML::link($CLICSHOPPING_Weight->link('ClassEdit&page=' . $page . '&wID=' . $Qweight->valueInt('weight_class_id') . '&tID=' . $Qweight->valueInt('weight_class_to_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Weight->getDef('icon_edit')));
                  echo '&nbsp;';
                  echo HTML::link($CLICSHOPPING_Weight->link('WeightEdit&page=' . $page . '&wID=' . $Qweight->valueInt('weight_class_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/copy.gif', $CLICSHOPPING_Weight->getDef('icon_edit_class_title')));
                  echo '&nbsp;';
                  echo HTML::link($CLICSHOPPING_Weight->link('ClassDelete&page=' . $page . '&wID=' . $Qweight->valueInt('weight_class_id') . '&tID=' . $Qweight->valueInt('weight_class_to_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Weight->getDef('icon_delete')));
                  echo '&nbsp;';
                ?>
              </td>
              </tr>

              <?php
            } // end while
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
            class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qweight->getPageSetLabel($CLICSHOPPING_Weight->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-md-right text-md-right"><?php echo $Qweight->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    }
  ?>
</div>
