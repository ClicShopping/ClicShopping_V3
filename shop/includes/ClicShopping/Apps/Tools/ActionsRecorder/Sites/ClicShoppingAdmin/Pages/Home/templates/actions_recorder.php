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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;

  $CLICSHOPPING_ActionsRecorder = Registry::get('ActionsRecorder');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));
  $directory_array = [];

  if ($dir = @dir(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/')) {
    while ($file = $dir->read()) {
      if (!is_dir(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/' . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  for ($i = 0, $n = count($directory_array); $i < $n; $i++) {
    $file = $directory_array[$i];

//    $CLICSHOPPING_Language->loadDefinitions($CLICSHOPPING_Template->getPathLanguageShopDirectory() . '/' . $CLICSHOPPING_Language->get('directory') . '/modules/action_recorder'  . $module_type . '/' . pathinfo($file, PATHINFO_FILENAME));

    include($CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/action_recorder/' . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (class_exists($class)) {
      $GLOBALS[$class] = new $class;
    }
  }

  $modules_array = [];
  $modules_list_array = array(array('id' => '',
    'text' => $CLICSHOPPING_ActionsRecorder->getDef('text_all_modules')
    )
  );

  $Qmodules = $CLICSHOPPING_Db->get('action_recorder', 'distinct module', null, 'module');

  while ($Qmodules->fetch()) {
    $modules_array[] = $Qmodules->value('module');

    $modules_list_array[] = ['id' => $Qmodules->value('module'),
      'text' => (is_object($GLOBALS[$Qmodules->value('module')]) ? $GLOBALS[$Qmodules->value('module')]->title : $Qmodules->value('module'))
    ];
  }
?>


<!-- body //-->
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <?php echo HTML::form('search', $CLICSHOPPING_ActionsRecorder->link('ActionsRecorder'), 'post', null, ['session_id' => true]); ?>

        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/modules_action_recorder.gif', $CLICSHOPPING_ActionsRecorder->getDef($CLICSHOPPING_ActionsRecorder->getDef('heading_title')), '40', '40'); ?></span>
          <span
            class="col-md-6 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ActionsRecorder->getDef('heading_title'); ?></span>
          <span class="col-md-4 text-md-right">
            <div class="form-group">
              <div class="controls">
<?php
  echo HTML::form('search', $CLICSHOPPING_ActionsRecorder->link('ActionsRecorder'), 'post', 'class="form-inline"', ['session_id' => true]);
  //  echo HTML::inputField('search', null, 'id="search" placeholder="' . $CLICSHOPPING_ActionsRecorder->getDef('text_filter_search') . '"');
  echo HTML::selectField('module', $modules_list_array, null, 'onchange="this.form.submit();"');
?>
              </div>
            </div>
          </span>
          <span class="col-md-1 text-md-right">
            <?php echo HTML::button($CLICSHOPPING_ActionsRecorder->getDef('button_reset'), null, $CLICSHOPPING_ActionsRecorder->link('ActionsRecorder&Expire' . (isset($_POST['module']) && in_array($_POST['module'], $modules_array) ? '&module=' . $_POST['module'] : '')), 'danger'); ?>
          </span>
        </div>
        </form>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <table class="table table-sm table-hover table-striped">
    <thead>
    <tr class="dataTableHeadingRow">
      <th width="20">&nbsp;</th>
      <th><?php echo $CLICSHOPPING_ActionsRecorder->getDef('table_heading_module'); ?></th>
      <th><?php echo $CLICSHOPPING_ActionsRecorder->getDef('table_heading_customer'); ?></th>
      <th><?php echo $CLICSHOPPING_ActionsRecorder->getDef('table_heading_identifier'); ?></th>
      <th class="text-md-center"><?php echo $CLICSHOPPING_ActionsRecorder->getDef('table_heading_date_added'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
      $filter = [];

      if (isset($_POST['module']) && in_array($_POST['module'], $modules_array)) {
        $filter[] = 'module = :module';
      }

      if (isset($_POST['search']) && !empty($_POST['search'])) {
        $filter[] = 'identifier like :identifier';
      }

      $sql_query = 'select SQL_CALC_FOUND_ROWS * from :table_action_recorder';

      if (!empty($filter)) {
        $sql_query .= ' where ' . implode(' and ', $filter);
      }

      $sql_query .= ' order by date_added desc limit :page_set_offset, :page_set_max_results';

      $Qactions = $CLICSHOPPING_ActionsRecorder->db->prepare($sql_query);

      if (!empty($filter)) {
        if (isset($_POST['module']) && in_array($_POST['module'], $modules_array)) {
          $Qactions->bindValue(':module', $_POST['module']);
        }

        if (isset($_POST['search']) && !empty($_POST['search'])) {
          $Qactions->bindValue(':identifier', '%' . $_POST['search'] . '%');
        }
      }

      $Qactions->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
      $Qactions->execute();

      while ($Qactions->fetch()) {
        $module = $Qactions->value('module');

        $module_title = $Qactions->value('module');

        if (is_object($GLOBALS[$module])) {
          $module_title = $GLOBALS[$module]->title;
        }
        ?>
        <tr>
          <th scope="row"
              class="text-md-center"><?php echo(($Qactions->value('success') === 1) ? '<i class="fas fa-check fa-lg" aria-hidden="true"></i>' : '<i class="fas fa-times fa-lg" aria-hidden="true"></i>'); ?></th>
          <td><?php echo $module_title; ?></td>
          <td><?php echo $Qactions->valueProtected('user_name') . ' [' . (int)$Qactions->valueInt('user_id') . ']'; ?></td>
          <td><?php echo(!is_null($Qactions->value('identifier')) ? '<a href="' . $CLICSHOPPING_ActionsRecorder->link('ActionsRecorder&search=' . $Qactions->value('identifier')) . '"><u>' . $Qactions->valueProtected('identifier') . '</u></a>' : '(empty)'); ?></td>
          <td class="text-md-center"><?php echo DateTime::toShort($Qactions->value('date_added'), true); ?></td>
        </tr>
        <?php
      }
    ?>
    </tbody>
  </table>

  <div class="row">
    <div class="col-md-12">
      <div
        class="col-md-6 float-md-left pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qactions->getPageSetLabel($CLICSHOPPING_ActionsRecorder->getDef('text_display_number_of_link')); ?></div>
      <div
        class="float-md-right text-md-right"> <?php echo $Qactions->getPageSetLinks((isset($_POST['module']) && in_array($_POST['module'], $modules_array) && is_object($GLOBALS[$_POST['module']]) ? 'module=' . $_POST['module'] : null) . '&' . (isset($_POST['search']) && !empty($_POST['search']) ? 'search=' . $_POST['search'] : null)); ?></div>
    </div>
  </div>
</div>