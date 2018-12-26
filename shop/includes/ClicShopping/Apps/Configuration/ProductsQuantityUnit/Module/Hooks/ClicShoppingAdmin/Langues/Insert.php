<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit as ProductsQuantityUnitApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $insert_language_id;

    public function __construct() {
      if (!Registry::exists('ProductsQuantityUnit')) {
        Registry::set('ProductsQuantityUnit', new ProductsQuantityUnitApp());
      }

      $this->app = Registry::get('ProductsQuantityUnit');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
      $this->lang =  Registry::get('Language');
    }

    private function insert() {
      if (isset($this->insert_language_id)) {

        $QproductsQuantityUnit = $this->app->db->get('products_quantity_unit', '*', ['language_id' => $this->lang->getId()]);

        while ($QproductsQuantityUnit->fetch()) {
          $cols = $QproductsQuantityUnit->toArray();

          $cols['language_id'] = $this->insert_language_id;

          $this->app->db->save('products_quantity_unit', $cols);
        }
      }
    }

    public function execute() {
      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }