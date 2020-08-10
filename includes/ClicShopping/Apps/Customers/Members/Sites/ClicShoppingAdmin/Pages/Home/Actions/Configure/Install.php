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

  namespace ClicShopping\Apps\Customers\Members\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Members = Registry::get('Members');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Members->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('MembersAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Members->getDef('alert_module_install_success'), 'success', 'members');

      $CLICSHOPPING_Members->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Members = Registry::get('Members');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Members->db->get('administrator_menu', 'app_code', ['app_code' => 'app_customers_members']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 4,
          'link' => 'index.php?A&Customers\Members&Members',
          'image' => 'client_attente.gif',
          'b2b_menu' => 1,
          'access' => 0,
          'app_code' => 'app_customers_members'
        ];

        $insert_sql_data = ['parent_id' => 4];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Members->db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Members->db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Members->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Members->db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }
  }
