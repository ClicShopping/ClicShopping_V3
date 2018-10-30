<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
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
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
<?php
  echo HTML::form('upgrade', $CLICSHOPPING_Upgrade->link('ModuleInstall'), 'post', null, ['session_id' => true]);
?>
            <div class="col-md-12 form-group row">
              <div class="col-md-3">
                <div class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/apps.png', $CLICSHOPPING_Upgrade->getDef('heading_title'), '40', '40'); ?></div>
                <div class="col-md-11 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Upgrade->getDef('heading_title'); ?></div>
              </div>

              <div class="col-md-2"><?php echo HTML::selectMenu('install_module_directory', $CLICSHOPPING_Github->getModuleDirectory(), $_POST['template_directory'], 'onchange="this.form.submit();"'); ?></div>
              <div class="col-md-2"><?php echo HTML::selectMenu('install_module_template_directory', $CLICSHOPPING_Github->getModuleTemplateDirectory(), $_POST['template_directory'], 'onchange="this.form.submit();"'); ?></div>
              <div class="col-md-2"><?php echo HTML::inputField('module_search', '', 'required id="search" placeholder="' . $CLICSHOPPING_Upgrade->getDef('text_search') . '"'); ?></div>
              <div class="col-md-3 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_reset'), null, $CLICSHOPPING_Upgrade->link('Upgrade&ResetCache'), 'danger', null, 'sm') . '&nbsp;';
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_reset_temp'), null, $CLICSHOPPING_Upgrade->link('Upgrade&ResetCacheTemp'), 'warning', null, 'sm') . '&nbsp;';
?>
              </div>
            </div>

            <div class="col-md-12 form-group row">
              <div class="col-md-3"></div>
              <div class="col-md-9 text-md-center"><?php echo $CLICSHOPPING_Github->getDropDownMenuSearchOption(); ?></div>
            </div>
          </div>

          </form>
        </div>
      </div>
    </div>

    <div class="separator"></div>
    <div class="alert alert-info">
      <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Upgrade->getDef('title_help')) . ' ' . $CLICSHOPPING_Upgrade->getDef('title_help') ?></div>
      <div class="separator"></div>
<?php
  $core_online_info = $CLICSHOPPING_Github->getJsonCoreInformation();

  if ($current_version < $core_online_info->version) {
?>
      <div class="row">
      <span class="col-md-12 text-md-right">
<?php
  echo HTML::form('reset', $CLICSHOPPING_Upgrade->link('CoreReset'));
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_reset_cache_core'), null, null, 'danger', null, 'sm');
  echo '</form>';
?>
      </span>
      </div>
      <div class="row">
        <div class="col-md-12"><?php echo $CLICSHOPPING_Upgrade->getDef('text_upgrade_version') . CLICSHOPPING::getVersion(); ?></div>
        <div class="col-md-12">
          <?php echo $core_online_info->description; ?><br/>

        </div>
<?php
    if ($current_version < $core_online_info->version) {
?>
          <div class="col-md-12" style="color: #0000CC;">
            <strong>
              <?php
                echo $CLICSHOPPING_Upgrade->getDef('text_upgrade_new_version') . ' ' . $core_online_info->version . '<br />';
                echo 'Date : ' . $core_online_info->date . '<br />';
                echo 'Description : ' . $core_online_info->description . '<br />';
                echo 'Github : <a href="https://github.com/ClicShopping/ClicShopping_V3/archive/master.zip" target="_blank">' . $CLICSHOPPING_Upgrade->getDef('test_download') . '</a><br />';
              ?>
            </strong>
          </div>
<?php
    } else {
?>
        <div class="col-md-12">
              <span class="col-md-1">
<?php
      echo HTML::link($CLICSHOPPING_Upgrade->link('Upgrade'), HTML::button($CLICSHOPPING_Upgrade->getDef('test_download'), null, null, 'warning', null, 'sm')) . ' ';
?>
              </span>
          <span class="col-md-11">
<?php
      echo ' <a href="https://github.com/ClicShopping/ClicShopping_V3/" target="_blank">' . HTML::button('ClicShopping', null, null, 'primary', null, 'sm') . '</a>';
?>
            </span>
<?php
    }
?>
          <div class="separator"></div>
          <div><?php echo $CLICSHOPPING_Upgrade->getDef('text_upgrade_site'); ?><br />
            -  <a href="https://github.com/ClicShopping/ClicShopping_V3/" target="_blank"> ClicShopping</a><br />
            -  <a href="https://github.com/ClicShoppingOfficialModulesV3" target="_blank"><?php echo $CLICSHOPPING_Upgrade->getDef('text_official'); ?><br />
              - <a href="https://github.com/ClicShoppingV3Community" target="_blank"><?php echo $CLICSHOPPING_Upgrade->getDef('text_community'); ?><br />
          </div>
<?php
  } else {
?>
      <div class="row">
        <span class="col-md-12"><?php echo $CLICSHOPPING_Upgrade->getDef('text_up_to_date') . CLICSHOPPING::getVersion(); ?></span>
      </div>
<?php
  }
?>
    </div>
  </div>

