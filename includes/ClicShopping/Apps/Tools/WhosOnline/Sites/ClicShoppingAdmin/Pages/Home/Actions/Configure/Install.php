<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\WhosOnline\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_WhosOnline = Registry::get('WhosOnline');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_WhosOnline->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('WhosOnlineAdminConfig' . $current_module);
    $m->install();

    static::installDbMenuAdministration();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_WhosOnline->getDef('alert_module_install_success'), 'success', 'WhosOnline');

    $CLICSHOPPING_WhosOnline->redirect('Configure&module=' . $current_module);
  }

  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_WhosOnline = Registry::get('WhosOnline');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_tools_whos_online']);

    if ($Qcheck->fetch() === false) {

      $sql_data_array = ['sort_order' => 30,
        'link' => 'index.php?A&Tools\WhosOnline&WhosOnline',
        'image' => 'whos_online.gif',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_tools_whos_online'
      ];

      $insert_sql_data = ['parent_id' => 163];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {

        $language_id = $languages[$i]['id'];

        $sql_data_array = ['label' => $CLICSHOPPING_WhosOnline->getDef('title_menu')];

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
