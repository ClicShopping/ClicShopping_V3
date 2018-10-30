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

  namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
    }

    private function delete($id) {
      if (!is_null($id)) {
        $this->app->db->delete('suppliers_info', ['languages_id' => $id]);
      }
    }

    public function execute() {
      if (!defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['DeleteConfirm'])) {
        $id = HTML::sanitize($_GET['lID']);
        $this->delete($id);
      }
    }
  }