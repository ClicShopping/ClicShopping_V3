<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Manufacturers;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Manufacturers');
    }

    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Wysiwyg = Registry::get('Wysiwyg');

      $languages = $CLICSHOPPING_Language->getLanguages();

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      $manufacturers_name = HTML::sanitize($_POST['manufacturers_name']);
      $manufacturers_image = HTML::sanitize($_POST['manufacturers_image']);
      $suppliers_id = 0;

// Insertion images des fabricants via l'éditeur FCKeditor (fonctionne sur les nouvelles et éditions des fabricants)
      if (isset($_POST['manufacturers_image']) && !\is_null($_POST['manufacturers_image']) && ($_POST['manufacturers_image'] != 'none') && (!isset($_POST['delete_image']))) {
        $manufacturers_image = HTMLOverrideAdmin::getWysiwygImageAlone($manufacturers_image);
      } else {
        $manufacturers_image = null;
      }

      $sql_data_array = ['manufacturers_name' => $manufacturers_name,
        'suppliers_id' => $suppliers_id
      ];

      if (\is_null($manufacturers_image)) {
        $sql_data_array = ['manufacturers_image' => $manufacturers_image];
      }

      $insert_sql_data = ['date_added' => 'now()'];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $this->app->db->save('manufacturers', $sql_data_array);

      $manufacturers_id = $this->app->db->lastInsertId();


      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $manufacturers_url_array = HTML::sanitize($_POST['manufacturers_url']);
        $manufacturer_description_array = HTML::sanitize($_POST['manufacturer_description']);
        $manufacturer_seo_title_array = HTML::sanitize($_POST['manufacturer_seo_title']);
        $manufacturer_seo_description_array = HTML::sanitize($_POST['manufacturer_seo_description']);
        $manufacturer_seo_keyword_array = HTML::sanitize($_POST['manufacturer_seo_keyword']);
        $language_id = $languages[$i]['id'];

        $sql_data_array = ['manufacturers_id' => $manufacturers_id];

        $insert_sql_data = ['manufacturers_url' => HTML::sanitize($manufacturers_url_array[$language_id]),
          'languages_id' => (int)$language_id,
          'manufacturer_description' => $manufacturer_description_array[$language_id],
          'manufacturer_seo_title' => HTML::sanitize($manufacturer_seo_title_array[$language_id]),
          'manufacturer_seo_description' => HTML::sanitize($manufacturer_seo_description_array[$language_id]),
          'manufacturer_seo_keyword' => HTML::sanitize($manufacturer_seo_keyword_array[$language_id]),
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('manufacturers_info', $sql_data_array);
      }

      $CLICSHOPPING_Hooks->call('Manufacturers', 'Save');

      $this->app->redirect('Manufacturers&page=' . $page . '&mID=' . $manufacturers_id);
    }
  }