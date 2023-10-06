<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\ModulesHooks\Sql\MariaDb;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class MariaDb
{
  public function execute()
  {
    $CLICSHOPPING_ModulesHooks = Registry::get('ModulesHooks');
    $CLICSHOPPING_ModulesHooks->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installMenuAdministration();
  }

/**
* @return void
 */
  private static function installMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_ModulesHooks = Registry::get('ModulesHooks');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_tools_modules_hooks']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = [
        'sort_order' => 2,
        'link' => 'index.php?A&Tools\ModulesHooks&ModulesHooks',
        'image' => 'hooks.png',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_tools_modules_hooks'
      ];

      $insert_sql_data = ['parent_id' => 727];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_ModulesHooks->getDef('title_menu')];

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