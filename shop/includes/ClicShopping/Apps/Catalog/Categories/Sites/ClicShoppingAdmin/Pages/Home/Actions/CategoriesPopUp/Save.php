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

  namespace ClicShopping\Apps\Catalog\Categories\Sites\ClicShoppingAdmin\Pages\Home\Actions\CategoriesPopUp;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Save extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Categories');
    }

    public function execute() {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_POST['select_category_id'])) {
        $select_category_id = HTML::sanitize($_POST['select_category_id']);

        $sql_data_array = ['parent_id' => $select_category_id ];
        $insert_sql_data = ['date_added' => 'now()'];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('categories', $sql_data_array);

        $categories_id = $this->app->db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {
          $categories_name_array = $_POST['categories_name'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = ['categories_name' => HTML::sanitize($categories_name_array[$language_id])];

          $insert_sql_data = [
                              'categories_id' => (int)$categories_id,
                              'language_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $this->app->db->save('categories_description', $sql_data_array);
        }

        $CLICSHOPPING_Hooks->call('CategoriesPopUp', 'Insert');

        echo '<style>color: #c4071b;</style>Success';
        //    echo "From Server : ".json_encode($_POST)."<br>";
      } else {
        echo 'Error <br />';
      }

      exit;
    }
  }