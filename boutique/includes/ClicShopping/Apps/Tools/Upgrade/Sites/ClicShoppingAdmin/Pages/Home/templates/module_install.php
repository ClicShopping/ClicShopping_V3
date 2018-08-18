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
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Github;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Upgrade = Registry::get('Upgrade');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Github = new Github();
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if ($CLICSHOPPING_MessageStack->exists('header')) {
    echo $CLICSHOPPING_MessageStack->get('header');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
<?php
  echo HTML::form('upgrade', $CLICSHOPPING_Upgrade->link('ModuleInstall'), 'post', null, ['session_id' => true]);
?>
            <div class="form-group row">
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
  echo HTML::button($CLICSHOPPING_Upgrade->getDef('button_back'), null, $CLICSHOPPING_Upgrade->link('Upgrade'), 'primary', null, 'sm') . '&nbsp;';
?>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 text-md-center">
<?php
                  echo HTML::radioField('official', 'true', true, 'id="official1" autocomplete="off"') . $CLICSHOPPING_Upgrade->getDef('text_official') . ' ';
                  echo HTML::radioField('official', 'false', false, 'id="official2" autocomplete="off"') . $CLICSHOPPING_Upgrade->getDef('text_community');
?>
              </div>
            </div>
          </div>

          </form>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
<?php
  if (isset($_POST['module_search'])) {
    $module_directory = $_POST['module_search'];
  } elseif (isset($_GET['template_directory'])) {
    $module_directory = $_POST['template_directory'];
  }

  if (isset($_POST['install_module_template_directory'])) {
    $module_directory = $_POST['install_module_template_directory'];
  } elseif (isset( $_POST['install_module_directory'])) {
    $module_directory = $_POST['install_module_directory'];
  }

  if (isset($module_directory)) {
     $result = $CLICSHOPPING_Github->getSearchInsideRepo(); // @todo implement cache

    if ($CLICSHOPPING_Github->getSearchTotalCount() == 0) {
?>
      <div class="alert alert-warning">
<?php
          echo $CLICSHOPPING_Upgrade->getDef('warning_no_module');
          exit;
?>
      </div>
<?php
    } else {
?>
    <div class="alert alert-warning">
<?php
        echo $CLICSHOPPING_Upgrade->getDef('text_count_search') . ' ' . $CLICSHOPPING_Github->getSearchTotalCount();
?>
    </div>
<?php
    }
?>
    <div class="d-flex flex-wrap">

<?php
    $count = $CLICSHOPPING_Github->getSearchTotalCount();

    for ($i=0, $n=$count; $i<$n; $i++) {
      $item = $result['items'][$i];
      $module_real_name = $item['name'];
      $link_html =  $item['html_url'];

      $local_version = '';
      $temp_version = '';
      $temp_check = false;
      $installed_check = false;

      if (!is_null($CLICSHOPPING_Github->getCacheFile($module_real_name . '.json')) === true || !is_null($CLICSHOPPING_Github->getCacheFileTemp($module_real_name . '.json'))) {
        if (!is_null($CLICSHOPPING_Github->getCacheFile($module_real_name . '.json')) === true ) {
          $result_module_real_name = $CLICSHOPPING_Github->getCacheFile($module_real_name . '.json');
          $file_cache_information =  '<span class="badge badge-primary"> - File Installed Cached</span>';

          $item = $result_module_real_name;
          $content_module_name = $item->title . '.json';
          $local_version = $CLICSHOPPING_Upgrade->getDef('text_installed_version')  . ' <span class="badge badge-primary">'. $item->version . '</span>';
          $description = $item->description;
          $installed_check = true;
        } else {
          $result_module_real_name = $CLICSHOPPING_Github->getCacheFileTemp($module_real_name . '.json');
          $file_cache_information =  $CLICSHOPPING_Upgrade->getDef('text_local_version') . ' <span class="badge badge-info">  - Temp Cached</span>';

          $item = $result_module_real_name;
          $content_module_name = $item->title . '.json';
          $local_version = $CLICSHOPPING_Upgrade->getDef('text_temp_version') . ' <span class="badge badge-info">' . $item->version . '</span>';
          $description = $item->description;
          $temp_check = true;
        }


        if (!is_null($CLICSHOPPING_Github->getCacheFile($module_real_name . '.json')) === true ) {
          $result_module_real_name = $CLICSHOPPING_Github->getCacheFileTemp($module_real_name . '.json');
          $temp_version = $CLICSHOPPING_Upgrade->getDef('text_temp_version') . ' <span class="badge badge-info">' . $item->version . '</span>';
        }

        if ($content_module_name == $module_real_name . '.json') {
?>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <span class="col-md-12">
                  <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/header/logo_clicshopping.png', '50', '50'); ?>
                  <?php echo $module_real_name; ?></span><?php echo $file_cache_information; ?>
                </span>
              </div>
              <div class="card-block">
              <div class="row">
                <div class="card-text">
                  <div class="col-md-12"><?php echo $description; ?></div>
                  <div class="col-md-12" style="color: #FF0000;"><?php echo $local_version; ?></div>
                  <div class="col-md-12" style="color: #312eff;"><?php echo $temp_version; ?></div>

                  <div class="col-md-6 float-md-left">
                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#myModal_<?php echo $i;?>"><?php echo $CLICSHOPPING_Upgrade->getDef('button_more_infos'); ?></button>
                    <!-- Modal -->
                    <div id="myModal_<?php echo $i;?>" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title"><a href="<?php echo $link_html; ?>/archive/master.zip"><?php echo $module_real_name; ?></a>'; ?></h4>
                          </div>
                          <div class="modal-body">
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_description') . $description; ?></p>
                            <p>
<?php
          if (strtolower($item->type) == 'apps') {
            echo $CLICSHOPPING_Upgrade->getDef('text_activate') . ' : ' . HTTP::typeUrlDomain('ClicShoppingAdmin')  . 'index.php?A&' . $item->module_directory . '\\' . $result_content_module->apps_name;
          }
?>
                            </p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_licence') . $item->license; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_vendor') . $item->authors[0]->name; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_tag') . $item->tag; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_module_type') . $item->type;?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_directory_install') . $item->install . $item->module_directory; ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_more_infos') . '<a href="' . $link_html .'" target="_blank" rel="noreferrer">Github</a>';  ?></p>
                            <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_download') . '<a href="' . $link_html .'/archive/master.zip">' . $module_real_name . '</a>';  ?></p>
                            <p><img src="https://raw.github.com/ClicShoppingAddsOn/<?php echo $module_real_name; ?>/master/<?php echo $item->image; ?>" alt="<?php echo $module_real_name; ?>" class="img-fluid"></img></p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $CLICSHOPPING_Upgrade->getDef('text_close'); ?></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="float-md-right">
                    <div class="col-md-12">
<?php
          if ($temp_check === true) {
            echo HTML::form('install', $CLICSHOPPING_Upgrade->link('Upgrade&ModuleInstall'));

            $error = false;

            if (strtolower($item->is_free) == 'no') {
              if (!empty($item->website_link_to_sell)) {
                if (strpos("https://www.clicshopping.org/forum/files/file/", "https://www.clicshopping.org")) {
                  $message = $CLICSHOPPING_Upgrade->getDef('error_link_not_allowed');
                  $error = true;
                } else {
                  $marketplace_link = $item->website_link_to_sell;
                }
              }

              if ($error === true) {
                echo  '<div class="text-md-right"> ' . $message . '</div>';
              } else {
                echo  '<div class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_not_free'), null, $marketplace_link,  'primary', ['newwindow' => 'blank'], 'sm') . '</div>';
              }
            } else {
              if (strtolower($item->is_free) == 'yes') {
                echo  '<div class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_install'), null, null, 'warning', null, 'sm') . '</div>';
              }
            }

            echo HTML::hiddenField('githubLink', $link_html .'/archive/master.zip');
            echo HTML::hiddenField('type_module', $item->type_module);
            echo HTML::hiddenField('module_real_name', $module_real_name);
            echo HTML::hiddenField('module_directory',$module_directory);
            echo '</form>';
          }

          if (strtolower($item->type) == 'apps') {
            $module = CLICSHOPPING::link('index.php', 'A&' . $item->module_directory . '\\' . $item->apps_name);
          } else {
            $module = 'modules.php?set=' . $item->module_directory . '&list=new';
          }

          if ($local_version != -1) {
            if ($installed_check === true) {
              echo '<span class="text-align-right">' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_setting'), null, $module, 'success', null, 'sm') . '</span>';
            }
          }
?>
                    </div>
                  </div>
<?php
        }
      } else {
//****************************************
//  Github version
//****************************************
?>
                  <div class="col-md-4">
                    <div class="card">
                      <div class="card-header">
                        <span class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/header/logo_clicshopping.png', '50', '50'); ?></span>
                        <span class="col-md-11"><a href="<?php echo $link_html; ?>/archive/master.zip"><?php echo $module_real_name . ' - Github'; ?></a></span>
                      </div>
                      <div class="card-block">
                        <div class="row">
                          <div class="card-text">
<?php
        $result_module_real_name = $CLICSHOPPING_Github->getJsonRepoContentInformationModule($module_real_name);

        if (is_array($result_module_real_name)) {
         foreach ($result_module_real_name as $content) {
           $content_module_name = $content->name;
           $content_module_sha = $content->sha;

           if ($content_module_name == $module_real_name . '.json') {
              $result_content_module = $CLICSHOPPING_Github->getJsonModuleInformaton($content->download_url);
              $description = $result_content_module->description;
              $current_version_github = $result_content_module->version;
?>
                            <div class="col-md-12"><?php echo $result_content_module->description; ?></div>
                            <div class="col-md-12"><?php echo $CLICSHOPPING_Upgrade->getDef('text_server_version') . $current_version_github; ?></div>

                            <div class="col-md-6 float-md-left">
                              <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#myModal_<?php echo $i;?>"><?php echo $CLICSHOPPING_Upgrade->getDef('button_more_infos'); ?></button>
                              <!-- Modal -->
                              <div id="myModal_<?php echo $i; ?>" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                  <!-- Modal content-->
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h4 class="modal-title"><?php echo $module_real_name; ?></h4>
                                    </div>
                                    <div class="modal-body">
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_description') . $result_content_module->description; ?></p>
                                      <p>
<?php
          if ($result_content_module->type == 'apps' || $result_content_module->type == 'Apps') {
            echo $CLICSHOPPING_Upgrade->getDef('text_activate') . ' : ' . HTTP::typeUrlDomain('ClicShoppingAdmin')  . 'index.php?A&' . $result_content_module->activate_link . $result_content_module->module_directory . '\\' . $result_content_module->apps_name;
          }
?>
                                      </p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_licence') . $result_content_module->license; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_vendor') . $result_content_module->authors[0]->name; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_tag') . $result_content_module->tag; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_module_type') . $result_content_module->type;?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_directory_install') . $result_content_module->install; ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_dependance') . ' '. $result_content_module->dependance;  ?></p>
                                      <p><?php echo $CLICSHOPPING_Upgrade->getDef('text_more_infos') . '<a href="' . $link_html .'" target="_blank" rel="noreferrer">Github</a>'; ?></p>
                                      <p>
<?php
          if (strtolower($result_content_module->is_free) != 'no') {
            echo $CLICSHOPPING_Upgrade->getDef('text_download') . '<a href="' . $link_html .'/archive/master.zip">' . $module_real_name . '</a>';
          }
?>
                                      </p>
                                      <p><img src="https://raw.github.com/ClicShoppingOfficialModulesV3/<?php echo $module_real_name; ?>/master/<?php echo $result_content_module->image; ?>" alt="<?php echo $module_real_name; ?>" class="img-fluid"></img></p>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $CLICSHOPPING_Upgrade->getDef('text_close'); ?></button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6 text-md-right float-md-right">
<?php
            echo HTML::form('install', $CLICSHOPPING_Upgrade->link('Upgrade&ModuleInstall'));

            if (strtolower($result_content_module->is_free) != 'yes') {
              echo  '<span class="text-md-right"><a href="' . $result_content_module->website_link_to_sell . '" target="_blank" class="btn btn-success btn-sm active" role="button" aria-pressed="true">' . $CLICSHOPPING_Upgrade->getDef('button_not_free') . '</a></span>';
            } else {
              echo  '<span class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_install'), null, null, 'warning', null, 'sm') . '</span>';
            }

             if (strtolower($result_content_module->is_core) == 'yes') {
               echo  '<span class="text-md-right"> ' . HTML::button($CLICSHOPPING_Upgrade->getDef('button_core'), null, null, 'danger', null, 'sm') . '</span>';
             }

            echo HTML::hiddenField('type_module', $result_content_module->type_module);
            echo HTML::hiddenField('module_real_name', $module_real_name);
            echo HTML::hiddenField('module_directory',$module_directory);
            echo '</form>';

            if (strtolower($result_content_module->type) == 'apps') {
              $module = CLICSHOPPING::link('index.php', 'A&' . $result_content_module->module_directory . '\\' . $result_content_module->apps_name);
            } else {
              $module = 'modules.php?set=' . $result_content_module->module_directory . '&list=new';
            }
?>
          </div>
<?php
          }
        }
      } else {
?>
                <div class="col-md-12">
                  <div class="alert alert-warning">
                    <?php echo $CLICSHOPPING_Upgrade->getDef('error_rate_exceed'); ?>
                  </div>
                </div>
<?php
      }
    }
?>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
    }
  } else {
?>
  <div class="alert alert-info">
    <div class="row">
        <span class="col-md-12">
          <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Upgrade->getDef('title_help')); ?>
          <strong><?php echo '&nbsp;' . $CLICSHOPPING_Upgrade->getDef('title_help'); ?></strong>
        </span>
    </div>
    <div class="separator"></div>
    <div class="row">
      <span class="col-md-12"><?php echo $CLICSHOPPING_Upgrade->getDef('text_install_files'); ?></span>
    </div>
    <div class="separator"></div>
    <div class="row">
        <span class="col-md-12">
<?php
  echo $CLICSHOPPING_Upgrade->getDef('text_search_limit') . ' ' . $CLICSHOPPING_Github->getSearchLimit() . '<br />';
  echo $CLICSHOPPING_Upgrade->getDef('text_core_limit') . ' ' . $CLICSHOPPING_Github->getCoreLimit() . '<br />';
  echo $CLICSHOPPING_Upgrade->getDef('text_cache_file');
?>
        </span>
    </div>
  </div>
<?php
}

?>
    </div>
  </div>
</div>