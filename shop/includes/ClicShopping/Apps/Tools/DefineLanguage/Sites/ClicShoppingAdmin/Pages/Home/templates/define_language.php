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
  use ClicShopping\OM\FileSystem;

  $CLICSHOPPING_DefineLanguage = Registry::get('DefineLanguage');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = HTML::sanitize($_POST['search']);
  }

  $languages = $CLICSHOPPING_Language->getLanguages();

  if ($CLICSHOPPING_MessageStack->exists('define_language')) {
    echo $CLICSHOPPING_MessageStack->get('define_language');
  }

  if (!FileSystem::isWritable($CLICSHOPPING_Template->getDirectoryPathLanguage())) {
?>
    <div class="alert alert-warning"
         role="alert"><?php echo $CLICSHOPPING_DefineLanguage->getDef('error_language_directory_not_writeable'); ?></div>
<?php
  }
?>
<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/define_language.gif', $CLICSHOPPING_DefineLanguage->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-6 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_DefineLanguage->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-md-right">
<?php
  echo HTML::form('search_form', $CLICSHOPPING_DefineLanguage->link('DefineLanguage'), 'post', 'class="form-inline"', ['session_id' => true]) . HTML::inputField('search', null, 'placeholder="' . $CLICSHOPPING_DefineLanguage->getDef('text_search') . '"') . ' ';
  echo '&nbsp;';

  /*
    if (isset($_POST['search'])) {
      echo HTML::button($CLICSHOPPING_DefineLanguage->getDef('button_back'), null, $CLICSHOPPING_DefineLanguage->link('DefineLanguage'), 'primary');
    }
  */
  if (!isset($_POST['search'])) {
    echo '&nbsp;';
    echo ' ' . HTML::button($CLICSHOPPING_DefineLanguage->getDef('button_reset_all_languages'), null, $CLICSHOPPING_DefineLanguage->link('DefineLanguage&TableReset'), 'danger');
  }
?>
              </form>
          </span>
          <span class="col-md-1 text-md-right">
<?php
  if (isset($_POST['search'])) {
    echo HTML::button($CLICSHOPPING_DefineLanguage->getDef('button_reset'), null, $CLICSHOPPING_DefineLanguage->link('DefineLanguage'), 'warning');
  }
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <table class="table table-hover">
    <thead>
    <tr class="dataTableHeadingRow">
      <th class="col-md-8"><?php echo $CLICSHOPPING_DefineLanguage->getDef('table_heading_content_group_title'); ?></th>
      <th class="col-md-4 action"></th>
    </tr>
    </thead>
    <tbody>
    <?php
      if (isset($search)) {
        $Qcontent_group = $CLICSHOPPING_DefineLanguage->db->prepare("select distinct content_group
                                                                from :table_languages_definitions
                                                                where content_group like " . "'%" . $search . "%'" . " or definition_key like " . "'%" . $search . "%'" . " or definition_value like " . "'%" . $search . "%'" . "
                                                               ");
      } else {
        $Qcontent_group = $CLICSHOPPING_DefineLanguage->db->prepare('select distinct content_group
                                                                  from :table_languages_definitions
                                                                 ');
      }
      /*
            $Qcontent_group = $CLICSHOPPING_DefineLanguage->db->prepare('select SQL_CALC_FOUND_ROWS distinct content_group
                                                                   from :table_languages_definitions
                                                                   limit :page_set_offset,
                                                                         :page_set_max_results
                                                                  ');
      
            $Qcontent_group->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      */

      $Qcontent_group->execute();

      //      $listingTotalRow = $Qcontent_group->getPageSetTotalRows();

      //      if ($listingTotalRow > 0) {

      while ($Qcontent_group->fetch()) {
        $search_count = '';

        if (isset($search)) {
          $Qcontents = $CLICSHOPPING_DefineLanguage->db->prepare("select languages_id,
                                                                      count(*) as count
                                                               from :table_languages_definitions
                                                               where content_group = :content_group
                                                               and (definition_key like " . "'%" . $search . "%'" . " or definition_value like " . "'%" . $search . "%'" . ")
                                                               group by languages_id
                                                             ");
          $Qcontents->bindValue(':content_group', $Qcontent_group->value('content_group'));
          $Qcontents->execute();
          do {
            if ($Qcontents->valueInt('count') > 0) {
              for ($i = 0, $n = count($languages); $i < $n; $i++) {
                if ($languages[$i]['id'] == $Qcontents->value('languages_id')) {
                  $search_count .= ' [' . $languages[$i]['code'] . ':' . $Qcontents->valueInt('count') . ']';
                }
              }
            }
          } while ($Qcontents->fetch());
        }
        ?>
        <tr>
          <td><?php echo $Qcontent_group->value('content_group') . ($search_count > '' ? '<span class="text-info"><small><i>' . $search_count . '</i></small></span>' : ''); ?></td>
          <td class="action text-md-right">
            <?php
              if ($search_count > '') {
                echo HTML::link($CLICSHOPPING_DefineLanguage->link('ContentGroup=' . $Qcontent_group->value('content_group') . '&search=' . $search), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/filter.png', $CLICSHOPPING_DefineLanguage->getDef('image_filter')));
              }

              echo HTML::link($CLICSHOPPING_DefineLanguage->link('ContentGroup=' . $Qcontent_group->value('content_group')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_DefineLanguage->getDef('image_edit')));
            ?>
          </td>
        </tr>
        <?php
      }
      //      }
    ?>
    </tbody>
  </table>
  <?php
    /*
      if ($listingTotalRow > 0) {
    ?>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qcontent_group->getPageSetLabel($CLICSHOPPING_DefineLanguage->getDef('text_display_number_of_link')); ?></div>
              <div class="float-md-right text-md-right"> <?php echo $Qcontent_group->getPageSetLinks(); ?></div>
            </div>
          </div>
    
    
    <?php
      } // end $listingTotalRow
    */
  ?>
</div>