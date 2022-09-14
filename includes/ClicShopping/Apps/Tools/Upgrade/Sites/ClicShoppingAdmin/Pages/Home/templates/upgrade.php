<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Github;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Upgrade = Registry::get('Upgrade');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_Github = new Github();

  $current_version = CLICSHOPPING::getVersion();
  preg_match('/^(\d+\.)?(\d+\.)?(\d+)$/', $current_version, $version);

  if (isset($_POST['template_directory'])) {
    $template_directory = HTML::sanitize($_POST['template_directory']);
  } else {
    $template_directory = '';
  }

  $check_new_version = false;
  $core_online_info = $CLICSHOPPING_Github->getJsonCoreInformation();

  if (\is_object($core_online_info) && $core_online_info->version) {
    if ($current_version < $core_online_info->version) {
      $check_new_version = true;
    }
  }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row col-md-12">
          <?php echo HTML::form('upgrade', $CLICSHOPPING_Upgrade->link('ModuleInstall'), 'post', null, ['session_id' => true]); ?>
          <div class="col-md-12 form-group row">
            <div class="row">
              <span class="col-md-3">
                <span
                  class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/apps.png', $CLICSHOPPING_Upgrade->getDef('heading_title'), '40', '40'); ?></span>
                <span
                  class="col-md-11 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Upgrade->getDef('heading_title'); ?></span>
              </span>
              <span
                class="col-md-2"><?php echo HTML::selectMenu('install_module_directory', $CLICSHOPPING_Github->getModuleDirectory(), $template_directory); ?></span>
              <span
                class="col-md-2"><?php echo HTML::selectMenu('install_module_template_directory', $CLICSHOPPING_Github->getModuleTemplateDirectory(), $template_directory); ?></span>
              <span
                class="col-md-2"><?php echo HTML::inputField('module_search', '', 'id="search" placeholder="' . $CLICSHOPPING_Upgrade->getDef('text_search') . '"'); ?></span>
              <span class="col-md-3 text-end">
<?php
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_reset'), null, $CLICSHOPPING_Upgrade->link('Upgrade&ResetCache'), 'danger', null, 'sm') . '&nbsp;';
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_reset_temp'), null, $CLICSHOPPING_Upgrade->link('Upgrade&ResetCacheTemp'), 'warning', null, 'sm') . '&nbsp;';
?>
              </span>
            </div>
          </div>
          <div class="row col-md-12">
            <div class="col-md-12 form-group row">
              <span class="col-md-4"></span>
              <span
                class="col-md-4 text-center"><?php echo $CLICSHOPPING_Github->getDropDownMenuSearchOption(); ?></span>
              <span
                class="col-md-4"><?php echo HTML::button($CLICSHOPPING_Upgrade->getDef('text_search'), null, null, 'primary'); ?></span>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="card">
    <div class="card-header">
      <h4><?php echo $CLICSHOPPING_Upgrade->getDef('text_check_latest_release'); ?></h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-sm-4">
          <div class="card">
            <div class="card-body" style="height:10rem;">
              <h5 class="card-title"><i class="bi bi-clock-history"></i> <?php echo $CLICSHOPPING_Upgrade->getDef('text_current_version'); ?></h5>
              <p class="card-text">
                <h4>
                  <?php echo $current_version; ?>
                </h4>
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="card">
            <div class="card-body" style="height:10rem;">
              <h5 class="card-title"><i class="bi bi-git"></i> <?php echo $CLICSHOPPING_Upgrade->getDef('text_latest_version'); ?></h5>
              <p class="card-text">
                <h4>
                <?php
                  if ($check_new_version === true) {
                ?>
                 <div class="row">
                   <div class="col-md-12 text-start"><?php echo $core_online_info->version; ?></div>
                   <div class="col-md-12 text-center">
                     <?php //echo HTML::link($CLICSHOPPING_Upgrade->link('CoreUpgrade'), HTML::button($CLICSHOPPING_Upgrade->getDef('button_automatic_install'), null, null, 'danger', null, 'sm')); ?>
                     <?php echo '<a href="https://github.com/ClicShopping/ClicShopping_V3/archive/master.zip" target="_blank" rel="nofollow">' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_manual'), null, null, 'primary', null, 'sm') . '</a>'; ?>
                   </div>
                 </div>
                <?php
                  } else {
                    echo $CLICSHOPPING_Upgrade->getDef('text_uptodate');
                  }
                ?>
                </h4>
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="card">
            <div class="card-body" style="height:10rem;">
              <h5 class="card-title"><i class="bi bi-calendar2-check"></i> <?php echo $CLICSHOPPING_Upgrade->getDef('text_latest_release_date'); ?></h5>
              <p class="card-text">
                <h4>
                <?php
                  if ($check_new_version === true) {
                    echo $core_online_info->date;
                  }
                ?>
              </h4>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="card">
    <div class="card-header">
      <h5><i class="bi bi-file-diff"></i> <?php echo $CLICSHOPPING_Upgrade->getDef('text_changelog'); ?></h5>
    </div>
    <div class="card-body">
      <p class="card-text">
        <?php
          if ($check_new_version === true) {
            echo $core_online_info->description;
          }
        ?>
      </p>
    </div>
  </div>
  <div class="separator"></div>
  <div class="alert alert-info" role="alert">
      <?php
      echo '<h4><i class="bi bi-question-circle" title="' . $CLICSHOPPING_Upgrade->getDef('title_help') . '"></i></h4>' . $CLICSHOPPING_Upgrade->getDef('title_help') . '<br />';
      echo $CLICSHOPPING_Upgrade->getDef('text_upgrade_site') . '<br />';
      echo '- <a href=https://github.com/ClicShopping/ClicShopping_V3/releases" target="_blank" rel="noreferrer">ClicShopping Release</a><br />';
      echo '- <a href="https://github.com/ClicShoppingOfficialModulesV3" target="_blank" rel="noreferrer">' . $CLICSHOPPING_Upgrade->getDef('text_official') . '</a><br />';
      echo '- <a href="https://github.com/ClicShoppingV3Community" target="_blank" rel="noreferrer">' . $CLICSHOPPING_Upgrade->getDef('text_community') . '</a><br />';
      ?>
  </div>
</div>