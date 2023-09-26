<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditDesign\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_EditDesign = Registry::get('EditDesign');

    $current_module = $this->page->data['current_module'];
    $m = Registry::get('EditDesignAdminConfig' . $current_module);
    $m->uninstall();

    static::removeMenu();

    Cache::clear('menu-administrator');

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_EditDesign->getDef('alert_module_uninstall_success'), 'success', 'EditDesign');

    $CLICSHOPPING_EditDesign->redirect('Configure&module=' . $current_module);
  }

  private static function removeMenu(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_tools_data_base_tables']);

    if ($Qcheck->fetch()) {

      $QMenuId = $CLICSHOPPING_Db->prepare('select id
                                        from :table_administrator_menu
                                        where app_code = :app_code
                                      ');

      $QMenuId->bindValue(':app_code', 'app_tools_data_base_tables');
      $QMenuId->execute();

      $menu = $QMenuId->fetchAll();

      $menu1 = \count($menu);

      for ($i = 0, $n = $menu1; $i < $n; $i++) {
        $CLICSHOPPING_Db->delete('administrator_menu_description', ['id' => (int)$menu[$i]['id']]);
      }

      $CLICSHOPPING_Db->delete('administrator_menu', ['app_code' => 'app_tools_data_base_tables']);
    }
  }
}