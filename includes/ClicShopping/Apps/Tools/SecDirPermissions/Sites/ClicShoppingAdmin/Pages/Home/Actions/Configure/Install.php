<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecDirPermissions\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_SecDirPermissions = Registry::get('SecDirPermissions');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_SecDirPermissions->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('SecDirPermissionsAdminConfig' . $current_module);
    $m->install();

    static::installDbMenuAdministration();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_SecDirPermissions->getDef('alert_module_install_success'), 'success', 'SecDirPermissions');

    $CLICSHOPPING_SecDirPermissions->redirect('Configure&module=' . $current_module);
  }

  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_SecDirPermissions = Registry::get('SecDirPermissions');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_tools_sec_dir_permissions']);

    if ($Qcheck->fetch() === false) {

      $sql_data_array = ['sort_order' => 30,
        'link' => 'index.php?A&Tools\SecDirPermissions&SecDirPermissions',
        'image' => 'file_manager.gif',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_tools_sec_dir_permissions'
      ];

      $insert_sql_data = ['parent_id' => 178];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {

        $language_id = $languages[$i]['id'];

        $sql_data_array = ['label' => $CLICSHOPPING_SecDirPermissions->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
      }

      Cache::clear('menu-administrator');
    }
  }
}
