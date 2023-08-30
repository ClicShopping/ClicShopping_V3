<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Recommendations = Registry::get('Recommendations');

    $current_module = $this->page->data['current_module'];
    $m = Registry::get('RecommendationsAdminConfig' . $current_module);
    $m->uninstall();

    static::removeMenu();
    static::removeProductsRecommendationsDb();

    Cache::clear('menu-administrator');

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Recommendations->getDef('alert_module_uninstall_success'), 'success', 'Recommendations');

    $CLICSHOPPING_Recommendations->redirect('Configure&module=' . $current_module);
  }

  /**
   *
   */
  private static function removeMenu(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_recommendations']);

    if ($Qcheck->fetch()) {
      $QMenuId = $CLICSHOPPING_Db->prepare('select id
                                              from :table_administrator_menu
                                              where app_code = :app_code
                                            ');

      $QMenuId->bindValue(':app_code', 'app_marketing_recommendations');
      $QMenuId->execute();

      $menu = $QMenuId->fetchAll();

      $menu1 = \count($menu);

      for ($i = 0, $n = $menu1; $i < $n; $i++) {
        $CLICSHOPPING_Db->delete('administrator_menu_description', ['id' => (int)$menu[$i]['id']]);
      }

      $CLICSHOPPING_Db->delete('administrator_menu', ['app_code' => 'app_marketing_recommendations']);
    }
  }

  /**
   *
   */
  private static function removeProductsRecommendationsDb(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_products_recommendations"');

    if ($Qcheck->fetch() !== false) {
      $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_products_recommendations');
      $Qdelete->execute();

      $Qdelete = $CLICSHOPPING_Db->prepare('delete from :table_products_recommendations_to_categories');
      $Qdelete->execute();
    }
  }
}