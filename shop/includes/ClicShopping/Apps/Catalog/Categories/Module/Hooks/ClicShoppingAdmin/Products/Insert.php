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

  namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Catalog\Categories\Categories as CategoriesApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new CategoriesApp());
      }

      $this->app = Registry::get('Categories');
    }

    private function saveProductCategory($current_category_id)
    {
      if (isset($_GET['Insert'])) {

        $current_category_id = $current_category_id[0];

        $Qproducts = $this->app->db->prepare('select products_id
                                              from :table_products
                                              order by products_id desc
                                              limit 1
                                              ');
        $Qproducts->execute();

        $id = $Qproducts->valueInt('products_id');

        $sql_array = ['products_id' => (int)$id,
          'categories_id' => (int)$current_category_id
        ];

        $this->app->db->save('products_to_categories', $sql_array);
      }

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      if (isset($_POST['move_to_category_id'])) {
        $current_category_id = HTML::sanitize($_POST['move_to_category_id']);

        $this->saveProductCategory($current_category_id);
      }
    }
  }