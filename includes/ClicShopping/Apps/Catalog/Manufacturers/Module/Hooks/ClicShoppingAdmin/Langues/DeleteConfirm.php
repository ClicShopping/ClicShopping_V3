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

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Manufacturers')) {
        Registry::set('Manufacturers', new ManufacturersApp());
      }

      $this->app = Registry::get('Manufacturers');
    }

    private function delete($id)
    {
      if (!\is_null($id)) {
        $this->app->db->delete('manufacturers_info', ['languages_id' => $id]);
      }
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS') || CLICSHOPPING_APP_MANUFACTURERS_CM_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['DeleteConfirm'])) {
        $id = HTML::sanitize($_GET['lID']);
        $this->delete($id);
      }
    }
  }