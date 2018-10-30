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

  namespace ClicShopping\Apps\Catalog\Products\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $lang;
    protected $insert_language_id;

    public function __construct()   {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->lang = Registry::get('Language');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
    }

    private function insert() {

      if (isset($this->insert_language_id)) {
        $Qproducts = $this->app->db->prepare('select p.products_id as orig_product_id,
                                                     pd.*
                                              from :table_products p left join :table_products_description pd on p.products_id = pd.products_id
                                              where pd.language_id = :language_id
                                              ');

        $Qproducts->bindInt(':language_id', $this->lang->getId());
        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $cols = $Qproducts->toArray();

          $cols['products_id'] = $cols['orig_product_id'];
          $cols['language_id'] = $this->insert_language_id;
          $cols['products_viewed'] = 0;

          unset($cols['orig_product_id']);

          $this->app->db->save('products_description', $cols);
        }
      }
    }

    public function execute() {
      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }