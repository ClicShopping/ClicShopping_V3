<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


  namespace ClicShopping\Apps\Payment\MoneyOrder\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;

  class Install extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_MoneyOrder = Registry::get('MoneyOrder');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_MoneyOrder->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('MoneyOrderAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_MoneyOrder->getDef('alert_module_install_success'), 'success', 'MoneyOrder');

      $CLICSHOPPING_MoneyOrder->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_MoneyOrder = Registry::get('MoneyOrder');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_payment_moneyorder']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 4,
                           'link' => 'index.php?A&Payment\MoneyOrder&Configure',
                           'image' => 'modules_payment.gif',
                           'b2b_menu' => 0,
                           'access' => 1,
                           'app_code' => 'app_payment_moneyorder'
                          ];

        $insert_sql_data = ['parent_id' => 186];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i=0, $n=count($languages); $i<$n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_MoneyOrder->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
                              'language_id' => (int)$language_id
                              ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array );

        }

        Cache::clear('menu-administrator');
      }
    }
  }
