<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Modules\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Modules = Registry::get('Modules');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('ModulesAdminConfig' . $current_module);
      $m->uninstall();

      static::removeMenu();
      static::removeProductsModulesDb();

      Cache::clear('menu-administrator');
      Cache::clear('configuration');

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Modules->getDef('alert_module_uninstall_success'), 'success', 'Modules');

      $CLICSHOPPING_Modules->redirect('Configure&module=' . $current_module);
    }

    private static function removeMenu() :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_configuration_modules']);

      if ($Qcheck->fetch()) {

        $QMenuId = $CLICSHOPPING_Db->prepare('select id
                                              from :table_administrator_menu
                                              where app_code = :app_code
                                            ');

        $QMenuId->bindValue(':app_code', 'app_configuration_modules');
        $QMenuId->execute();

        $menu = $QMenuId->fetchAll();

        $menu1 = \count($menu);

        for ($i = 0, $n = $menu1; $i < $n; $i++) {
          $CLICSHOPPING_Db->delete('administrator_menu_description', ['id' => (int)$menu[$i]['id']]);
        }

        $CLICSHOPPING_Db->delete('administrator_menu', ['app_code' => 'app_configuration_modules']);
      }
    }

    private static function removeProductsModulesDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_currencies"');

      if ($Qcheck->fetch() !== false) {
        $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_currencies');
        $Qdelete->execute();
      }
    }
  }