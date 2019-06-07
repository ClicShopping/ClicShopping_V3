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


  namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions\ManufacturersPopUp;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  class Save extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Manufacturers');
    }

    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!empty($_POST['manufacturers_name'])) {

        $manufacturers_name = HTML::sanitize($_POST['manufacturers_name']);
        $manufacturers_image = HTML::sanitize($_POST['manufacturers_image']);

        if (isset($_POST['manufacturers_image']) && !is_null($_POST['manufacturers_image']) && ($_POST['manufacturers_image'] != 'none') && (!isset($_POST['delete_image']))) {
          $manufacturers_image = htmlspecialchars($manufacturers_image);
          $manufacturers_image = strstr($manufacturers_image, $CLICSHOPPING_Template->getDirectoryShopTemplateImages());
          $manufacturers_image = str_replace($CLICSHOPPING_Template->getDirectoryShopTemplateImages(), '', $manufacturers_image);
          $manufacturers_image_end = strstr($manufacturers_image, '&quot;');
          $manufacturers_image = str_replace($manufacturers_image_end, '', $manufacturers_image);
          $manufacturers_image = str_replace($CLICSHOPPING_Template->getDirectoryShopSources(), '', $manufacturers_image);
        } else {
          $manufacturers_image = 'null';
        }

        $sql_data_array = ['manufacturers_name' => $manufacturers_name];

        if (!is_null($manufacturers_image)) {
          $insert_image_sql_data = ['manufacturers_image' => $manufacturers_image];
          $sql_data_array = array_merge($sql_data_array, $insert_image_sql_data);
        }

        $insert_sql_data = ['date_added' => 'now()'];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('manufacturers', $sql_data_array);

        $manufacturers_id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {
          $manufacturers_url_array = $_POST['manufacturers_url'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = ['manufacturers_id' => $manufacturers_id];


          $insert_sql_data = ['manufacturers_url' => HTML::sanitize($manufacturers_url_array[$language_id]),
            'languages_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('manufacturers_info', $sql_data_array);

          $CLICSHOPPING_Hooks->call('Manufacturer', 'Insert');
        }

        Cache::clear('manufacturers');

        echo 'Success';
//    echo "From Server : ".json_encode($_POST)."<br>";
      } else {
        echo 'Error <br />';
      }

      exit;
    }
  }