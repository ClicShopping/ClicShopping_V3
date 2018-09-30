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

  namespace ClicShopping\Apps\Catalog\Suppliers\Sites\ClicShoppingAdmin\Pages\Home\Actions\SuppliersPopUp;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Save extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Suppliers');
    }

    public function execute() {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!empty($_POST['suppliers_name'])) {
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
        $suppliers_image = HTML::sanitize($_POST['suppliers_image']);

        // Insertion images des fabricants via l'editeur FCKeditor (fonctionne sur les nouvelles et editions des fabricants)
        if (isset($_POST['suppliers_image']) && !is_null($_POST['suppliers_image']) && ($_POST['suppliers_image'] != 'none') && ($_POST['delete_image'] != 'yes')) {
          $suppliers_image = htmlspecialchars($suppliers_image);
          $suppliers_image = strstr($suppliers_image, $CLICSHOPPING_Template->getDirectoryShopTemplateImages());
          $suppliers_image = str_replace($CLICSHOPPING_Template->getDirectoryShopTemplateImages(), '', $suppliers_image);
          $suppliers_image_end = strstr($suppliers_image, '&quot;');
          $suppliers_image = str_replace($suppliers_image_end, '', $suppliers_image);
          $suppliers_image = str_replace($CLICSHOPPING_Template->getDirectoryShopSources(), '', $suppliers_image);
        } else {
          $suppliers_image = 'null';
        }

        $sql_data_array = [
                            'suppliers_name' => $suppliers_name,
                            'suppliers_manager' => $suppliers_manager,
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

        if (!is_null($suppliers_image)) {
          $insert_image_sql_data = ['suppliers_image' => $suppliers_image];
          $sql_data_array = array_merge($sql_data_array, $insert_image_sql_data);
        }

        $insert_sql_data = ['date_added' => 'now()'];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('suppliers', $sql_data_array);

        $suppliers_id = $this->app->db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {
          $suppliers_url_array = $_POST['suppliers_url'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = ['suppliers_url' => HTML::sanitize($suppliers_url_array[$language_id])];

          $insert_sql_data = [
                              'suppliers_id' => (int)$suppliers_id,
                              'languages_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $this->app->db->save('suppliers_info', $sql_data_array);
        }

        $CLICSHOPPING_Hooks->call('SuppliersPopUp', 'Insert');

        echo 'Success';
        //    echo "From Server : ".json_encode($_POST)."<br>";
      } else {
        echo 'Error <br />';
      }

      exit;
    }
  }