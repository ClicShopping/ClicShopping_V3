<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Gdpr\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Gdpr = Registry::get('Gdpr');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_Gdpr->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('GdprAdminConfig' . $current_module);
    $m->install();

    static::installDbMenuAdministration();

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Gdpr->getDef('alert_module_install_success'), 'success', 'gdpr');

    $CLICSHOPPING_Gdpr->redirect('Configure&module=' . $current_module);
  }

  private static function installDbMenuAdministration(): void
  {
    $CLICSHOPPING_Gdpr = Registry::get('Gdpr');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Gdpr->db->get('administrator_menu', 'app_code', ['app_code' => 'app_customers_gdpr']);

    if ($Qcheck->fetch() === false) {

      $sql_data_array = [
        'sort_order' => 8,
        'link' => 'index.php?A&Customers\Gdpr&Gdpr',
        'image' => 'gdpr.gif',
        'b2b_menu' => 1,
        'access' => 0,
        'app_code' => 'app_customers_gdpr'
      ];

      $insert_sql_data = ['parent_id' => 4];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Gdpr->db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Gdpr->db->lastInsertId();

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {

        $language_id = $languages[$i]['id'];

        $sql_data_array = ['label' => $CLICSHOPPING_Gdpr->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Gdpr->db->save('administrator_menu_description', $sql_data_array);
      }

      Cache::clear('menu-administrator');
    }
  }
}
