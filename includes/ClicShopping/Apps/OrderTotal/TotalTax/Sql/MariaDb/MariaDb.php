<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\TotalTax\Sql\MariaDb;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class MariaDb
{
  /**
   * Executes the setup process for the TotalTax module.
   *
   * This method retrieves the TotalTax registry object, loads its language definitions,
   * and calls the static method to handle the installation of menu administration entries.
   *
   * @return void
   */
  public function execute()
  {
    $CLICSHOPPING_TotalTax = Registry::get('TotalTax');
    $CLICSHOPPING_TotalTax->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installMenuAdministration();
  }

  /**
   * Installs the menu administration entry for the Total Tax module in the administrator menu.
   *
   * @return void
   */
  private static function installMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_TotalTax = Registry::get('TotalTax');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_order_total_tax']);

    if ($Qcheck->fetch() === false) {
      $sql_data_array = ['sort_order' => 7,
        'link' => 'index.php?A&OrderTotal\TotalTax&Configure',
        'image' => 'modules_order_total.gif',
        'b2b_menu' => 0,
        'access' => 1,
        'app_code' => 'app_order_total_tax'
      ];

      $insert_sql_data = ['parent_id' => 451];
      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_TotalTax->getDef('title_menu')];

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