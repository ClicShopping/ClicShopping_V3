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


  namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions\AdministratorMenu;


  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;


  class Insert extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('AdministratorMenu');
    }

    public function execute() {
      $CLICSHOPPING_Language = Registry::get('Language');

      $sort_order = HTML::sanitize($_POST['sort_order']);
      $link = HTML::sanitize($_POST['link']);
      $image = HTML::sanitize($_POST['image']);
      $b2b_menu = HTML::sanitize($_POST['b2b_menu']);
      $access = HTML::sanitize($_POST['access_administrator']);

      if ($b2b_menu == 'on') {
        $b2b_menu = 1;
      } else {
        $b2b_menu = 0;
      }

      $current_category_id = HTML::sanitize($_POST['move_to_category_id']);

      $sql_data_array = ['sort_order' => (int)$sort_order,
                         'link' => $link,
                         'image' => $image,
                         'b2b_menu' => (int)$b2b_menu,
                         'access' => (int)$access,
                         'status' => 1
                        ];


      if (isset($_POST['current_category_id'])) {
        $current_category_id = $_POST['current_category_id'];
      }

      $insert_sql_data = ['parent_id' => (int)$current_category_id,
                          'app_code' => Null
                          ];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $this->app->db->save('administrator_menu', $sql_data_array);

      $id = $this->app->db->lastInsertId();

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i=0, $n=count($languages); $i<$n; $i++) {

        $label_array = $_POST['label'];

        $language_id = $languages[$i]['id'];

        $sql_data_array = ['label' => HTML::sanitize($label_array[$language_id])];

        $insert_sql_data = ['id' => $id,
                            'language_id' => $languages[$i]['id']
                           ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('administrator_menu_description', $sql_data_array );
      }

      Cache::clear('menu-administrator');

      $this->app->redirect('AdministratorMenu&cPath=' . $_GET['cPath'] . '&cID=' . (int)$id);
    }
  }