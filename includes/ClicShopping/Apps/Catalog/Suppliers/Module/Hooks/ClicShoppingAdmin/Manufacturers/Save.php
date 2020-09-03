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

  namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Manufacturers;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

  class Save implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Manufacturers'])) {
        $suppliers_id = HTML::sanitize($_POST['suppliers_id']);
        if (empty($suppliers_id)) $suppliers_id = 0;

        $sql_data_array = ['suppliers_id' => $suppliers_id];

        if (isset($_GET['mID'])) {
          $manufacturers_id = HTML::sanitize($_GET['mID']);
          $update_sql_data= ['manufacturers_id' => (int)$manufacturers_id];

        } else {
          $Qmanufacturers = $this->app->db->prepare('select manufacturers_id 
                                                     from :table_manufacturers
                                                     order by manufacturers_id desc
                                                     limit 1
                                                    ');
          $Qmanufacturers->execute();

          $update_sql_data = ['manufacturers_id' => $Qmanufacturers->valueInt('manufacturers_id')];
        }

        $sql_data_array = array_merge($sql_data_array, $update_sql_data);

        $this->app->db->save('manufacturers', $sql_data_array, $update_sql_data);
      }
    }
  }