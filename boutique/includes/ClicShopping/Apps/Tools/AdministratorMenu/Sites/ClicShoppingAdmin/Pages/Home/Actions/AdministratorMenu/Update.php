<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions\AdministratorMenu;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;


  class Update extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('AdministratorMenu');
    }

    public function execute() {
      $CLICSHOPPING_Language = Registry::get('Language');
      if (isset($_POST['id'])) $id = HTML::sanitize($_POST['id']);

      if (empty($id)) {
        $id = HTML::sanitize($_GET['cID']);
      }

      $sort_order = HTML::sanitize($_POST['sort_order']);
      $link = HTML::sanitize($_POST['link']);
      $image = HTML::sanitize($_POST['image']);
      $b2b_menu = HTML::sanitize($_POST['b2b_menu']);
      $access = $_POST['access_administrator'];

      if ($b2b_menu == 'on') {
        $b2b_menu = 1;
      } else {
        $b2b_menu = 0;
      }

      $sql_data_array = ['sort_order' => (int)$sort_order,
                         'link' => $link,
                         'image' => $image,
                         'b2b_menu' => (int)$b2b_menu,
                         'access' => (int)$access
                        ];

      $this->app->db->save('administrator_menu', $sql_data_array, ['id' => (int)$id] );

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i=0, $n=count($languages); $i<$n; $i++) {

        $label_array = $_POST['label'];

        $language_id = $languages[$i]['id'];

        $sql_data_array = ['label' => HTML::sanitize($label_array[$language_id])];

        $this->app->db->save('administrator_menu_description', $sql_data_array, ['id' => (int)$id,
                                                                                'language_id' => (int)$languages[$i]['id']
                                                                                 ]
                            );
      }

      Cache::clear('menu-administrator');

      $this->app->redirect('AdministratorMenu&cPath=' . $_GET['cPath'] . '&cID=' . $id);
    }
  }