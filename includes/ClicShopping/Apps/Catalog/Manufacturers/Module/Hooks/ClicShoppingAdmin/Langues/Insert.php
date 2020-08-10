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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Manufacturers\Manufacturers as ManufacturersApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $insert_language_id;

    public function __construct()
    {
      if (!Registry::exists('Manufacturers')) {
        Registry::set('Manufacturers', new ManufacturersApp());
      }

      $this->app = Registry::get('Manufacturers');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      if (isset($this->insert_language_id)) {
        $Qmanufacturers = $this->app->db->prepare('select m.manufacturers_id as orig_manufacturer_id,
                                                          mi.*
                                                    from :table_manufacturers m left join :table_manufacturers_info mi on m.manufacturers_id = mi.manufacturers_id
                                                    where mi.languages_id = :languages_id
                                                  ');

        $Qmanufacturers->bindInt(':languages_id', $this->lang->getId());
        $Qmanufacturers->execute();

        while ($Qmanufacturers->fetch()) {
          $cols = $Qmanufacturers->toArray();

          $cols['manufacturers_id'] = $cols['orig_manufacturer_id'];
          $cols['languages_id'] = $this->insert_language_id;

          unset($cols['orig_manufacturer_id']);
          unset($cols['url_clicks']);
          unset($cols['date_last_click']);

          $this->app->db->save('manufacturers_info', $cols);
        }
      }
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS') || CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }