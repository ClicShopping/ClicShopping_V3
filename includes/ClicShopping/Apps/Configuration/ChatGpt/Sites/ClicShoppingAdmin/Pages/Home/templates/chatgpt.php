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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_ChatGpt = Registry::get('ChatGpt');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/chatgpt.gif', $CLICSHOPPING_ChatGpt->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ChatGpt->getDef('heading_title'); ?></span>
          <span class="col-md-7 text-end">
          <?php echo HTML::button($CLICSHOPPING_ChatGpt->getDef('button_configure'), null, $CLICSHOPPING_ChatGpt->link('Configure'), 'primary'); ?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- ################# -->
  <!-- Hooks Stats - just use execute function to display the hook-->
  <!-- ################# -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <?php echo $CLICSHOPPING_Hooks->output('Stats', 'StatsGpt', null, 'display'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- //################################################################################################################ -->
  <!-- //                                            LISTING                                                           -->
  <!-- //################################################################################################################ -->

  <?php echo HTML::form('delete_all', $CLICSHOPPING_ChatGpt->link('ChatGpt&DeleteAll') .'&page=' . $page); ?>

  <div id="toolbar" class="float-end">
    <button id="button" class="btn btn-danger"><?php echo $CLICSHOPPING_ChatGpt->getDef('button_delete'); ?></button>
  </div>

  <table
    id="table"
    data-toggle="table"
    data-icons-prefix="bi"
    data-icons="icons"
    data-id-field="selected"
    data-select-item-name="selected[]"
    data-click-to-select="true"
    data-sort-order="asc"
    data-sort-name="chatgpt"
    data-toolbar="#toolbar"
    data-buttons-class="primary"
    data-show-toggle="true"
    data-show-columns="true"
    data-mobile-responsive="true">

    <thead class="dataTableHeadingRow">
    <tr>
      <th data-checkbox="true" data-field="state"></th>
      <th data-field="selected" data-sortable="true"><?php echo $CLICSHOPPING_ChatGpt->getDef('table_heading_chatgpt_id'); ?></th>
      <th data-field="question" class="text-center"  data-sortable="true"><?php echo $CLICSHOPPING_ChatGpt->getDef('table_heading_chatgpt_question'); ?></th>
      <th data-field="response" class="text-center"><?php echo $CLICSHOPPING_ChatGpt->getDef('table_heading_chatgpt_response'); ?></th>
      <th data-field="date_added" data-sortable="true"><?php echo $CLICSHOPPING_ChatGpt->getDef('table_heading_chatgpt_date_added'); ?></th>
      <th data-field="action" data-switchable="false" class="text-end"><?php echo $CLICSHOPPING_ChatGpt->getDef('table_heading_action'); ?>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php
      $QchatGpt = $CLICSHOPPING_ChatGpt->db->prepare('select SQL_CALC_FOUND_ROWS gpt_id,
                                                                                 question,
                                                                                 response,
                                                                                 date_added
                                                      from :table_chatgpt
                                                      order by date_added
                                                      limit :page_set_offset, :page_set_max_results
                                                    ');

      $QchatGpt->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $QchatGpt->execute();

      $listingTotalRow = $QchatGpt->getPageSetTotalRows();

      if ($listingTotalRow > 0) {
        while ($QchatGpt->fetch()) {
      ?>
          <tr>
            <td></td>
            <td><?php echo $QchatGpt->valueInt('gpt_id'); ?></td>
            <td class="text-start"><?php echo $QchatGpt->value('question'); ?></td>
            <td class="text-start"><?php echo substr($QchatGpt->value('response'), 0, 200) . "..."; ?></td>
            <td><?php echo DateTime::toShort($QchatGpt->value('date_added')); ?></td>
            <td class="text-end">
              <div class="btn-group" role="group" aria-label="buttonGroup">
              <?php
                echo HTML::link($CLICSHOPPING_ChatGpt->link('Edit&page=' . $page . '&cID=' . $QchatGpt->valueInt('currencies_id')), '<h4><i class="bi bi-pencil" title="' . $CLICSHOPPING_ChatGpt->getDef('icon_edit') . '"></i></h4>');
                echo '&nbsp;';
              ?>
              </div>
            </td>
          </tr>
          <?php
        }
      }
    ?>
      </tbody>
    </table>
    </form>  
  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-start pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $QchatGpt->getPageSetLabel($CLICSHOPPING_ChatGpt->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-end text-end"><?php echo $QchatGpt->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    }
  ?>
</div>