<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Groups = Registry::get('Groups');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('GroupsAdminConfig' . $current_module);
      $m->uninstall();

      static::removeMenu();
      static::removeDataDb();

      Cache::clear('menu-administrator');

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Groups->getDef('alert_module_uninstall_success'), 'success', 'groups');

      $CLICSHOPPING_Groups->redirect('Configure&module=' . $current_module);
    }

    private static function removeMenu()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_customers_groups']);

      if ($Qcheck->fetch()) {

        $QMenuId = $CLICSHOPPING_Db->prepare('select id
                                        from :table_administrator_menu
                                        where app_code = :app_code
                                      ');

        $QMenuId->bindValue(':app_code', 'app_customers_groups');
        $QMenuId->execute();

        $menu = $QMenuId->fetchAll();

        $menu1 = \count($menu);

        for ($i = 0, $n = $menu1; $i < $n; $i++) {
          $CLICSHOPPING_Db->delete('administrator_menu_description', ['id' => (int)$menu[$i]['id']]);
        }

        $CLICSHOPPING_Db->delete('administrator_menu', ['app_code' => 'app_customers_groups']);
      }
    }

    private static function removeDataDb()
    {
      /*
            $CLICSHOPPING_Db = Registry::get('Db');
      
            $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_customers_groups"');
      
            if ($Qcheck->fetch() !== false) {
              $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_customers_groups');
              $Qdelete->execute();
            }
      
            $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_groups_to_categories"');
      
            if ($Qcheck->fetch() !== false) {
              $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_groups_to_categories');
              $Qdelete->execute();
            }
      
            $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_groups_to_categories"');
      
            if ($Qcheck->fetch() !== false) {
              $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_groups_to_categories');
              $Qdelete->execute();
            }
      */
    }
  }