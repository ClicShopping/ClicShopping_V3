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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Apps;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  use ClicShopping\Sites\ClicShoppingAdmin\CallUserFuncModule;

  require('includes/application_top.php');

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_CfgModule = Registry::get('CfgModulesAdmin');
  $CLICSHOPPING_Db = Registry::get('Db');

  $set = (isset($_GET['set']) ? $_GET['set'] : '');

  $modules = $CLICSHOPPING_CfgModule->getAll();

  if (empty($set) || !$CLICSHOPPING_CfgModule->exists($set)) {
    $set = $modules[0]['code'];
  }

  $module_type = $CLICSHOPPING_CfgModule->get($set, 'code');
  $module_directory = $CLICSHOPPING_CfgModule->get($set, 'directory');
  $module_language_directory = $CLICSHOPPING_CfgModule->get($set, 'language_directory');

  $module_site = $CLICSHOPPING_CfgModule->get($set, 'site');
  $module_key = $CLICSHOPPING_CfgModule->get($set, 'key');

  $template_integration = $CLICSHOPPING_CfgModule->get($set, 'template_integration');
  $language_template_module_directory = $CLICSHOPPING_CfgModule->get($set, 'languageTemplateModuleDirectory');

  define('HEADING_TITLE', $CLICSHOPPING_CfgModule->get($set, 'title'));

  $appModuleType = null;

  switch ($module_type) {
    case 'dashboard':
      $appModuleType = 'AdminDashboard';
    break;
    case 'header_tags':
      $appModuleType = 'HeaderTags';
    break;
    case 'payment':
      $appModuleType = 'Payment';
    break;

    case 'shipping':
      $appModuleType = 'Shipping';
    break;

    case 'order_total':
      $appModuleType = 'OrderTotal';
    break;
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!is_null($action)) {
    switch ($action) {
      case 'save':

          foreach( $_POST['configuration'] as $key => $value ) {
// Start Dynamic Template System
          if((is_array($value)) && (!empty($value))){
            $key = HTML::sanitize($key);
            $value = HTML::sanitize($value);

            $pages = '';
            $count = count($value);

            for($i=0 ; $i<$count; $i++){

              $pages = "$pages$value[$i]";

              $CLICSHOPPING_Db->save('configuration', ['configuration_value' => $pages], ['configuration_key' => $key]);

// END Dynamic Template System
            }
          } else {

            $CLICSHOPPING_Db->save('configuration', ['configuration_value' => $value], ['configuration_key' => $key]);
          }
        }

        Cache::clear('configuration');

        CLICSHOPPING::redirect('modules.php', 'set=' . $set . '&module=' . $_GET['module']);
        break;
      case 'install':
      case 'remove':

        if (strpos($_GET['module'], '\\') !== false) {
          $class = Apps::getModuleClass($_GET['module'], $appModuleType);

          if (class_exists($class)) {
            $file_extension = '';
            $module = new $class();
            $class = $_GET['module'];
          }
        } else {

          $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));
          $class = basename($_GET['module']);

          if (is_file($module_directory . $class . $file_extension)) {
            include($module_directory . $class . $file_extension);
            $module = new $class;
          }
        }

        if (isset($module)) {
          if ($action == 'install') {
            if ($module->check() > 0) { // remove module if already installed
              $module->remove();
            }

            $module->install();

            $modules_installed = explode(';', constant($module_key));

            if (!in_array($class . $file_extension, $modules_installed)) {
              $modules_installed[] = $class . $file_extension;
            }

            Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $modules_installed)],
                                                       ['configuration_key' => $module_key]
                                     );

            Cache::clear('configuration');
            CLICSHOPPING::redirect('modules.php', 'set=' . $set . '&module=' . $class);

          } elseif ($action == 'remove') {
            $module->remove();

            $modules_installed = explode(';', constant($module_key));

            if (in_array($class . $file_extension, $modules_installed)) {
              unset($modules_installed[array_search($class . $file_extension, $modules_installed)]);
            }

            Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $modules_installed)],
                                                       ['configuration_key' => $module_key]
                                      );

            Cache::clear('configuration');
            CLICSHOPPING::redirect('modules.php', 'set=' . $set);
          }
        }

      Cache::clear('configuration');
      CLICSHOPPING::redirect('modules.php', 'set=' . $set . '&module=' . $class);

      break;
    }
  }

  require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('header.php'));
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/products_unit.png', CLICSHOPPING::getDef('heading_title'), '40', '40'); ?></span>
          <span class="col-md-5 pageHeading"><?php echo '&nbsp;' . CLICSHOPPING::getDef('heading_title'); ?></span>
          <span class="col-md-6 text-md-right">
<?php
  if ($_GET['action'] == 'edit') {
    echo '<span class="cols-xs-3 float-right">';
    echo HTML::button(CLICSHOPPING::getDef('button_cancel'), null,  CLICSHOPPING::link('modules.php',  'set=' . $set), 'warning') .'&nbsp;';
    echo HTML::form('modules', CLICSHOPPING::link('modules.php', 'set=' . $set . '&module=' . $_GET['module'] . '&action=save'));
    echo HTML::button(CLICSHOPPING::getDef('button_update'), null, null, 'success');
    echo '</span>';
  } elseif (isset($_GET['list'])) {
    echo '            <span>' . HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link('modules.php', 'set=' . $set), 'primary') .'</span>';
  } else {
    echo '            <span>' . HTML::button(CLICSHOPPING::getDef('button_module_install'), null,  CLICSHOPPING::link('modules.php', 'set=' . $set . '&list=new'), 'success') . '</span>';
  }
  echo '            <span>' . HTML::button(CLICSHOPPING::getDef('button_search_module'), null,  CLICSHOPPING::link('index.php', 'A&Tools\Upgrade&Upgrade'), 'primary') . '</span>';
?>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
<?php
  $modules_installed = (defined($module_key) ? explode(';', constant($module_key)) : array());

  $new_modules_counter = 0;

  $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));
  $directory_array = [];

  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          if (isset($_GET['list']) && ($_GET['list'] == 'new')) {
            if (!in_array($file, $modules_installed)) {
              $directory_array[] = $file;
            }
          } else {
            if (in_array($file, $modules_installed)) {
              $directory_array[] = $file;
            } else {
              $new_modules_counter++;
            }
          }
        }
      }
    }
    $dir->close();
  }

  if (isset($appModuleType)) {
    foreach (Apps::getModules($appModuleType) as $k => $v) {
      if (isset($_GET['list']) && ($_GET['list'] == 'new')) {
        if (!in_array($k, $modules_installed)) {
          $directory_array[] = $k;
        }
      } else {
        if (in_array($k, $modules_installed)) {
          $directory_array[] = $k;
        } else {
          $new_modules_counter++;
        }
      }
    }
  }

  sort($directory_array);

  $installed_modules = [];

  if (empty($_GET['action'])) {
?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
          <tr class="dataTableHeadingRow">
            <th><?php echo CLICSHOPPING::getDef('table_heading_modules'); ?></th>
            <th class="text-md-center"><?php echo CLICSHOPPING::getDef('table_heading_sort_order'); ?></th>
            <th class="text-md-center"><?php echo CLICSHOPPING::getDef('table_heading_status'); ?></th>
            <th class="text-md-right"><?php echo CLICSHOPPING::getDef('table_heading_action'); ?>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
<?php
  $installed_modules = [];

  for ($i=0, $n=count($directory_array); $i<$n; $i++) {
    $file = $directory_array[$i];

    if (strpos($file, '\\') !== false) {
      $file_extension = '';

      $class = Apps::getModuleClass($file, $appModuleType);

      $module = new $class();
      $module->code = $file;

      $class = $file;

    } else {
      $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));

      if (is_file($module_language_directory . '/' . $CLICSHOPPING_Language->get('directory') . '/modules/'  . $module_type . '/' . pathinfo($file, PATHINFO_FILENAME) . '.php') ||  is_file($module_language_directory . '/' . $CLICSHOPPING_Language->get('directory') . '/modules/'  . $module_type . '/' . pathinfo($file, PATHINFO_FILENAME) . '.txt')) {
        $CLICSHOPPING_Language->loadDefinitions($module_site . '/modules/'  . $module_type . '/' . pathinfo($file, PATHINFO_FILENAME));
      } else {
        $CLICSHOPPING_Language->loadDefinitions($CLICSHOPPING_Template->getDirectoryPathShopDefaultTemplateHtml() . '/languages/' . $CLICSHOPPING_Language->get('directory') . '/modules/'  . $module_type . '/' . pathinfo($file, PATHINFO_FILENAME));
      }

      include($module_directory . $file);

      $class = substr($file, 0, strrpos($file, '.'));

      if (class_exists($class)) {
        $module = new $class;
        }
      }

    if (isset($module)) {
      if ($module->check() > 0) {
        if (($module->sort_order > 0) && !isset($installed_modules[$module->sort_order])) {
          $installed_modules[$module->sort_order] = $file;
        } else {
          $installed_modules[] = $file;
        }
      }

      if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $class))) && !isset($mInfo)) {
        $module_info = ['code' => $module->code,
                         'title' => $module->title,
                         'description' => $module->description,
                         'status' => $module->check(),
                         'signature' => (isset($module->signature) ? $module->signature : null),
                         'api_version' => (isset($module->api_version) ? $module->api_version : null)
                        ];

        $module_keys = $module->keys();

        $keys_extra = [];

        for ($j=0, $k=count($module_keys); $j<$k; $j++) {

          $Qkeys = $CLICSHOPPING_Db->get('configuration', [
                                                    'configuration_title',
                                                    'configuration_value',
                                                    'configuration_description',
                                                    'use_function',
                                                    'set_function'
                                                    ], [
                                                    'configuration_key' => $module_keys[$j]
                                                    ]
                                  );

          $keys_extra[$module_keys[$j]]['title'] = $Qkeys->value('configuration_title');
          $keys_extra[$module_keys[$j]]['value'] = $Qkeys->value('configuration_value');
          $keys_extra[$module_keys[$j]]['description'] = $Qkeys->value('configuration_description');
          $keys_extra[$module_keys[$j]]['use_function'] = $Qkeys->value('use_function');
          $keys_extra[$module_keys[$j]]['set_function'] = $Qkeys->value('set_function');
        }

        $module_info['keys'] = $keys_extra;

        $mInfo = new \ArrayObject($module_info, \ArrayObject::ARRAY_AS_PROPS);
      }
?>
                <td><?php echo $module->title; ?></td>
                <td class="text-md-center"><?php if (in_array($module->code . $file_extension, $modules_installed) && is_numeric($module->sort_order)) echo $module->sort_order; ?></td>
                <td class="text-md-center">
<?php
      if ($module->enabled == 1) {
        echo 'True';
      } else {
        echo '<span class="text-info">False</span>';
      }
?>
                </td>
                <td class="text-md-right">
<?php
      if ($module->check() > 0)  {
        echo HTML::link(CLICSHOPPING::link('modules.php', 'set=' . $set . '&module=' . $class . '&action=edit'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', CLICSHOPPING::getDef('icon_edit'))) . '&nbsp;';
        echo HTML::link(CLICSHOPPING::link('modules.php', 'set=' . $set . '&module=' . $class . '&action=remove'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/remove.gif', CLICSHOPPING::getDef('image_module_remove')));

      } else {
        echo HTML::link(CLICSHOPPING::link('modules.php', 'set=' . $set . '&module=' . $class . '&action=install'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/install.gif', CLICSHOPPING::getDef('image_module_install')));
      }
        echo '&nbsp;';
?>
                </td>
              </tr>
<?php
    }
  }

  if (!isset($_GET['list'])) {
    ksort($installed_modules);

    $Qcheck = $CLICSHOPPING_Db->get('configuration', 'configuration_value', ['configuration_key' => $module_key]);

    if ($Qcheck->fetch() !== false) {

      if ($Qcheck->value('configuration_value') != implode(';', $installed_modules)) {
        Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $installed_modules),
                                                    'last_modified' => 'now()'
                                                    ],
                                                    ['configuration_key' => $module_key]
                                  );
      }
    } else {

      $CLICSHOPPING_Db->save('configuration', [
                                          'configuration_title' => 'Installed Modules',
                                          'configuration_key' => $module_key,
                                          'configuration_value' => implode(';', $installed_modules) ,
                                          'configuration_description' => 'This is automatically updated. No need to edit.',
                                          'configuration_group_id' => 6,
                                          'sort_order' => 0,
                                          'date_added' => 'now()'
                                        ]
                      );

    }

    if ($template_integration === true) {
      $Qcheck = $CLICSHOPPING_Db->get('configuration', 'configuration_value', ['configuration_key' => 'TEMPLATE_BLOCK_GROUPS']);

      if ($Qcheck->fetch() !== false) {
        $tbgroups_array = explode(';', $Qcheck->value('configuration_value'));
        if (!in_array($module_type, $tbgroups_array)) {
          $tbgroups_array[] = $module_type;
          sort($tbgroups_array);

          $CLICSHOPPING_Db->save('configuration', [
                                          'configuration_value' => implode(';', $tbgroups_array),
                                          'last_modified' => 'now()'
                                        ],
                                        [
                                          'configuration_key' => 'TEMPLATE_BLOCK_GROUPS'
                                        ]
                         );
        }
      } else {
        $CLICSHOPPING_Db->save('configuration', [
                                            'configuration_title' => 'Installed Template Block Groups',
                                            'configuration_key' => 'TEMPLATE_BLOCK_GROUPS',
                                            'configuration_value' => $module_type,
                                            'configuration_description' => 'This is automatically updated. No need to edit.',
                                            'configuration_group_id' => 6,
                                            'sort_order' => 0,
                                            'date_added' => 'now()'
                                          ]
                        );

      }
    }
  }
?>
        </tbody>
      </table>
    </td>
  </table>
  <div class="col-md-12"><?php echo CLICSHOPPING::getDef('text_module_directory') . ' ' . $module_directory; ?></div>

<?php
  } else {

    for ($i=0, $n=count($directory_array); $i<$n; $i++) {
      $file = $directory_array[$i];
      if (strpos($file, '\\') !== false) {
        $file_extension = '';

        $class = Apps::getModuleClass($file, $appModuleType);

        $module = new $class();
        $module->code = $file;

        $class = $file;

      } else {
        $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));

        include($module_directory . $file);

        $class = substr($file, 0, strrpos($file, '.'));
        if (class_exists($class)) {
          $module = new $class;
        }
      }

      if (isset($module)) {
        if ($module->check() > 0) {
          if (($module->sort_order > 0) && !isset($installed_modules[$module->sort_order])) {
            $installed_modules[$module->sort_order] = $file;
          } else {
            $installed_modules[] = $file;
          }
        }

        if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $class))) && !isset($mInfo)) {
          $module_info = ['code' => $module->code,
                          'title' => $module->title,
                          'description' => $module->description,
                          'status' => $module->check(),
                          'signature' => (isset($module->signature) ? $module->signature : null),
                          'api_version' => (isset($module->api_version) ? $module->api_version : null)
                          ];

          $module_keys = $module->keys();

          $keys_extra = [];

          for ($j=0, $k=count($module_keys); $j<$k; $j++) {

            $keyValue = $CLICSHOPPING_Db->prepare('select configuration_title,
                                                          configuration_value,
                                                          configuration_description,
                                                          use_function,
                                                          set_function
                                                   from :table_configuration
                                                   where configuration_key = :configuration_key
                                                  ');
            $keyValue->bindValue(':configuration_key', $module_keys[$j]);
            $keyValue->execute();

            $key_value = $keyValue->fetch();

            $keys_extra[$module_keys[$j]]['title'] = $key_value['configuration_title'];
            $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
            $keys_extra[$module_keys[$j]]['description'] = $key_value['configuration_description'];
            $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
            $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
          }

          $module_info['keys'] = $keys_extra;

          $mInfo = new \ArrayObject($module_info, \ArrayObject::ARRAY_AS_PROPS);
        }
      }
    }


    if (isset($mInfo) && (strpos($mInfo->code, '\\') !== false)) {
      $file_extension = '';
    } else {
      $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));
    }

    $keys = '';
?>
    <div id="orderTabs" style="overflow: auto;">
      <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
        <li class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . CLICSHOPPING::getDef('tab_general') . '</a>'; ?></li>
      </ul>
      <div class="tabsClicShopping">
        <div class="tab-content">
          <div class="mainTitle"><?php echo CLICSHOPPING::getDef('text_box_heading_module'); ?></div>
          <div class="adminformTitle">
<?php
    foreach( $mInfo->keys as $key => $value ) {
      $keys .= '<strong>' . $value['title'] . '</strong><br />' . $value['description'] . '<br />';

      if ( strlen($value['set_function']) > 0 ) {
       $keys .= CallUserFuncModule::execute($value['set_function'],  $value['value'], $key);
      } else {
        $keys .= HTML::inputField('configuration[' . $key . ']', $value['value']);
      }
      $keys .= '<br /><br />';
    }

    $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
    echo $keys;
?>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php
  }
?>
<!-- body_eof //-->
</div>
<!-- footer //-->
<?php
  require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('footer.php'));
  require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('application_bottom.php'));
