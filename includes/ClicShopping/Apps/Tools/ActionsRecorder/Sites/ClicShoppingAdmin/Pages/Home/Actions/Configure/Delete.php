<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\ActionsRecorder\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ActionsRecorder = Registry::get('ActionsRecorder');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('ActionsRecorderAdminConfig' . $current_module);
      $m->uninstall();

      static::removeMenu();
      static::removeProductsActionsRecorderDb();

      Cache::clear('menu-administrator');

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ActionsRecorder->getDef('alert_module_uninstall_success'), 'success', 'ActionsRecorder');

      $CLICSHOPPING_ActionsRecorder->redirect('Configure&module=' . $current_module);
    }

    private static function removeMenu()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->get('actions_recorder', 'app_code', ['app_code' => 'app_tools_actions_recorder']);

      if ($Qcheck->fetch()) {

        $QMenuId = $CLICSHOPPING_Db->prepare('select id
                                        from :table_actions_recorder
                                        where app_code = :app_code
                                      ');

        $QMenuId->bindValue(':app_code', 'app_tools_actions_recorder');
        $QMenuId->execute();

        $menu = $QMenuId->fetchAll();

        $menu1 = \count($menu);

        for ($i = 0, $n = $menu1; $i < $n; $i++) {
          $CLICSHOPPING_Db->delete('actions_recorder_description', ['id' => (int)$menu[$i]['id']]);
        }

        $CLICSHOPPING_Db->delete('actions_recorder', ['app_code' => 'app_tools_actions_recorder']);
      }
    }

    private static function removeProductsActionsRecorderDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_actions_recorder"');

      if ($Qcheck->fetch() !== false) {
        $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_actions_recorder');
        $Qdelete->execute();
      }

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_actions_recorder_description"');

      if ($Qcheck->fetch() !== false) {
        $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_actions_recorder_description');
        $Qdelete->execute();
      }
    }
  }