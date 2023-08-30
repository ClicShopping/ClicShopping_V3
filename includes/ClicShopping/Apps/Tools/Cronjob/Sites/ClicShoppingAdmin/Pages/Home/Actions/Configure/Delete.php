<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Cronjob = Registry::get('Cronjob');

    $current_module = $this->page->data['current_module'];
    $m = Registry::get('CronjobAdminConfig' . $current_module);
    $m->uninstall();

    static::removeMenu();
    static::removeProductsCronjobDb();

    Cache::clear('menu-administrator');

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Cronjob->getDef('alert_module_uninstall_success'), 'success', 'Cronjob');

    $CLICSHOPPING_Cronjob->redirect('Configure&module=' . $current_module);
  }

  private static function removeMenu(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->get('cronjob', 'app_code', ['app_code' => 'app_tools_cronjob']);

    if ($Qcheck->fetch()) {

      $QMenuId = $CLICSHOPPING_Db->prepare('select id
                                        from :table_cronjob
                                        where app_code = :app_code
                                      ');

      $QMenuId->bindValue(':app_code', 'app_tools_cronjob');
      $QMenuId->execute();

      $menu = $QMenuId->fetchAll();

      $menu1 = \count($menu);

      for ($i = 0, $n = $menu1; $i < $n; $i++) {
        $CLICSHOPPING_Db->delete('cronjob_description', ['id' => (int)$menu[$i]['id']]);
      }

      $CLICSHOPPING_Db->delete('cronjob', ['app_code' => 'app_tools_cronjob']);
    }
  }

  private static function removeProductsCronjobDb()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_cronjob"');

    if ($Qcheck->fetch() !== false) {
      $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_cronjob');
      $Qdelete->execute();
    }

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_cronjob_description"');

    if ($Qcheck->fetch() !== false) {
      $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_cronjob_description');
      $Qdelete->execute();
    }
  }
}