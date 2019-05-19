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


  namespace ClicShopping\Apps\Catalog\Suppliers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Suppliers;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Suppliers');
    }

    public function execute()
    {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      if (isset($_GET['mID'])) $suppliers_id = HTML::sanitize($_GET['mID']);

      $suppliers_name = HTML::sanitize($_POST['suppliers_name']);
      $suppliers_manager = HTML::sanitize($_POST['suppliers_manager']);
      $suppliers_phone = HTML::sanitize($_POST['suppliers_phone']);
      $suppliers_email_address = HTML::sanitize($_POST['suppliers_email_address']);
      $suppliers_fax = HTML::sanitize($_POST['suppliers_fax']);
      $suppliers_address = HTML::sanitize($_POST['suppliers_address']);
      $suppliers_suburb = HTML::sanitize($_POST['suppliers_suburb']);
      $suppliers_postcode = HTML::sanitize($_POST['suppliers_postcode']);
      $suppliers_city = HTML::sanitize($_POST['suppliers_city']);
      $suppliers_states = HTML::sanitize($_POST['suppliers_states']);
      $suppliers_country_id = HTML::sanitize($_POST['suppliers_country_id']);
      $suppliers_notes = HTML::sanitize($_POST['suppliers_notes']);
      $suppliers_image = $_POST['suppliers_image'];

      $sql_data_array = ['suppliers_name' => $suppliers_name,
        'suppliers_manager' => $suppliers_manager,
        'date_added' => 'now()',
        'suppliers_phone' => $suppliers_phone,
        'suppliers_email_address' => $suppliers_email_address,
        'suppliers_fax' => $suppliers_fax,
        'suppliers_address' => $suppliers_address,
        'suppliers_suburb' => $suppliers_suburb,
        'suppliers_postcode' => $suppliers_postcode,
        'suppliers_city' => $suppliers_city,
        'suppliers_states' => $suppliers_states,
        'suppliers_country_id' => (int)$suppliers_country_id,
        'suppliers_notes' => $suppliers_notes
      ];

      $update_sql_data = ['last_modified' => 'now()'];
      $sql_data_array = array_merge($sql_data_array, $update_sql_data);

      $this->app->db->save('suppliers', $sql_data_array, ['suppliers_id' => (int)$suppliers_id]);

// Insertion images des fabricants via l'éditeur FCKeditor (fonctionne sur les nouvelles et éditions des fabricants)
      if (isset($_POST['suppliers_image']) && !is_null($_POST['suppliers_image']) && (!empty($_POST['suppliers_image'])) && ($_POST['delete_image'] != 'yes')) {
        $suppliers_image = HTMLOverrideAdmin::getCkeditorImageAlone($suppliers_image);

        $sql_data_array = ['suppliers_image' => $suppliers_image];
        $this->app->db->save('suppliers', $sql_data_array, ['suppliers_id' => (int)$suppliers_id]);
      }

// Suppression de l'image
      if ($_POST['delete_image'] == 'yes') {
        $sql_data_array = ['suppliers_image' => ''];
        $this->app->db->save('suppliers', $sql_data_array, ['suppliers_id' => (int)$suppliers_id]);
      }


      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $suppliers_url_array = $_POST['suppliers_url'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = ['suppliers_url' => HTML::sanitize($suppliers_url_array[$language_id])];

        $this->app->db->save('suppliers_info', $sql_data_array, ['suppliers_id' => (int)$suppliers_id,
            'languages_id' => (int)$language_id
          ]
        );
      }

      $CLICSHOPPING_Hooks->call('Suppliers', 'Update');

      $this->app->redirect('Suppliers&page=' . $page);
    }
  }