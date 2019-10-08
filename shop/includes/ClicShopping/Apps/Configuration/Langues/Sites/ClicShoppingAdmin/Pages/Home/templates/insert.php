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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\FileSystem;

  $CLICSHOPPING_Langues = Registry::get('Langues');

  $icons = [];

  foreach (glob(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/third_party/flag-icon-css/flags/4x3/*.svg') as $file) {
    $code = basename($file, '.svg');

    $icons[] = [
      'id' => $code,
      'text' => $code
    ];
  }

  $directories = [];

  foreach (glob(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/languages/*', GLOB_ONLYDIR) as $dir) {
    $code = basename($dir);

    $directories[] = ['id' => $code,
      'text' => $code
    ];
  }

  foreach (glob(CLICSHOPPING::getConfig('dir_root', 'ClicShoppingAdmin') . 'includes/languages/*', GLOB_ONLYDIR) as $dir) {
    $code = basename($dir);

    if (array_search($code, array_column($directories, 'id')) === false) {
      $directories[] = ['id' => $code,
        'text' => $code
      ];
    }
  }

  uasort($directories, function ($a, $b) {
    if ($a['id'] == $b['id']) {
      return 0;
    }

    return ($a['id'] < $b['id']) ? -1 : 1;
  });

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
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
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
<?php
    if (FileSystem::isWritable($CLICSHOPPING_Template->getDirectoryPathLanguage())) {
    ?>
    <div class="alert alert-warning"
         role="alert"><?php echo $CLICSHOPPING_Langues->getDef('error_language_directory_not_writeable'); ?></div>
    <?php
  }
?>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Langues->getDef('text_info_heading_new_language'); ?></strong></div>
  <?php echo HTML::form('languages', $CLICSHOPPING_Langues->link('Langues&Insert')); ?>
  <div class="adminformTitle">

    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Langues->getDef('text_info_insert_intro'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Langues->getDef('text_info_insert_intro'); ?></label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Langues->getDef('text_info_language_name'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Langues->getDef('text_info_language_name'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('name', null, 'required aria-required="true" placeholder="' . $CLICSHOPPING_Langues->getDef('text_info_language_name') . '"'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Langues->getDef('text_info_language_code'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Langues->getDef('text_info_language_code'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('code', null, 'required aria-required="true" placeholder="' . $CLICSHOPPING_Langues->getDef('text_info_language_code') . '"'); ?>
          </div>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Langues->getDef('text_info_language_image'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Langues->getDef('text_info_language_image'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::selectField('image', $icons); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Langues->getDef('text_info_language_directory'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Langues->getDef('text_info_language_directory'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::selectField('directory', $directories); ?>
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <?php echo HTML::inputField('directory_create', null, 'placeholder="' . $CLICSHOPPING_Langues->getDef('text_create_directory') . '"'); ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Langues->getDef('text_info_language_locale'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Langues->getDef('text_info_language_locale'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('locale', null, 'placeholder="' . $CLICSHOPPING_Langues->getDef('text_locale') . '" required aria-required="true"'); ?>
          </div>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-md-5">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_Langues->getDef('text_info_language_sort_order'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_Langues->getDef('text_info_language_sort_order'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::inputField('sort_order'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12">
        <span class="col-md-3"></span>
        <span
          class="col-md-4"><?php echo HTML::checkboxField('create_language') . '  ' . $CLICSHOPPING_Langues->getDef('text_create_language') . '<br />'; ?></span>
      </div>
    </div>
    <div class="separator"></div>
    <div class="col-md-12 text-md-center">
      <?php echo HTML::button($CLICSHOPPING_Langues->getDef('button_insert'), null, null, 'primary', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Langues->getDef('button_cancel'), null, $CLICSHOPPING_Langues->link('Langues&page=' . $page), 'warning', null, 'sm'); ?>
    </div>
    <div class="separator"></div>
  </div>

  </form>
  <div class="separator"></div>
  <div class="alert alert-info" role="alert">
    <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Langues->getDef('title_help')) . ' ' . $CLICSHOPPING_Langues->getDef('title_help') ?></div>
    <div class="separator"></div>
    <div><?php echo $CLICSHOPPING_Langues->getDef('text_note_create_language'); ?></div>
  </div>
</div>