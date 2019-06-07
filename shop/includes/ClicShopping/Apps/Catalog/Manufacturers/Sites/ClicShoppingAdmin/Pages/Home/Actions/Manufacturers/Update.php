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


  namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Manufacturers;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
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

      if (isset($_GET['mID'])) $manufacturers_id = HTML::sanitize($_GET['mID']);

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      $manufacturers_image = $_POST['manufacturers_image'];
      $manufacturers_name = HTML::sanitize($_POST['manufacturers_name']);
      $suppliers_id = HTML::sanitize($_POST['suppliers_id']);

      $sql_data_array = ['manufacturers_name' => $manufacturers_name,
        'suppliers_id' => $suppliers_id
      ];

      $update_sql_data = ['last_modified' => 'now()'];

      $sql_data_array = array_merge($sql_data_array, $update_sql_data);

      $this->app->db->save('manufacturers', $sql_data_array, ['manufacturers_id' => (int)$manufacturers_id]);

// Insertion images des fabricants via l'editeur FCKeditor (fonctionne sur les nouvelles et editions des fabricants)
      if (isset($_POST['manufacturers_image']) && !is_null($_POST['manufacturers_image']) && !empty($_POST['manufacturers_image']) && (!isset($_POST['delete_image']))) {
        $manufacturers_image = HTMLOverrideAdmin::getCkeditorImageAlone($manufacturers_image);

        $sql_data_array = ['manufacturers_image' => $manufacturers_image];

        $this->app->db->save('suppliers', $sql_data_array, ['manufacturers_id' => (int)$manufacturers_id]);
      }

// Suppression de l'image
      if ($_POST['delete_image'] == 'yes') {
        $sql_data_array = ['manufacturers_image' => ''];
        $this->app->db->save('suppliers', $sql_data_array, ['manufacturers_id' => (int)$manufacturers_id]);
      }

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $manufacturers_url_array = $_POST['manufacturers_url'];
        $manufacturer_description_array = $_POST['manufacturer_description'];
        $manufacturer_seo_title_array = $_POST['manufacturer_seo_title'];
        $manufacturer_seo_description_array = $_POST['manufacturer_seo_description'];
        $manufacturer_seo_keyword_array = $_POST['manufacturer_seo_keyword'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = ['manufacturers_url' => HTML::sanitize($manufacturers_url_array[$language_id]),
          'manufacturer_description' => $manufacturer_description_array[$language_id],
          'manufacturer_seo_title' => HTML::sanitize($manufacturer_seo_title_array[$language_id]),
          'manufacturer_seo_description' => HTML::sanitize($manufacturer_seo_description_array[$language_id]),
          'manufacturer_seo_keyword' => HTML::sanitize($manufacturer_seo_keyword_array[$language_id])
        ];

        $this->app->db->save('manufacturers_info', $sql_data_array, ['manufacturers_id' => (int)$manufacturers_id,
            'languages_id' => (int)$language_id
          ]
        );
      }

      $CLICSHOPPING_Hooks->call('Manufacturers', 'Update');

      $this->app->redirect('Manufacturers&page=' . $page . '&mID=' . $manufacturers_id);
    }
  }