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


  namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Catalog\Categories\Categories as CategoriesApp;

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin;

  class Update implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $productsAdmin;

    public function __construct()   {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new CategoriesApp());
      }

      $this->app = Registry::get('Categories');

      $this->productsAdmin = new ProductsAdmin();
    }

    private function UpdateProductCategories($id = null) {
      $CLICSHOPPING_MessageStack =  Registry::get('MessageStack');

      $new_category = HTML::sanitize($_POST['move_to_category_id']);
      $current_category = HTML::sanitize($_POST['cPath']);
      $products_link = HTML::sanitize($_POST['copy_as']);


      if (isset($_GET['Update'])) {
//link the category
        if (is_array($new_category) && isset($new_category)) {
          foreach ($new_category as $value_id) {
           $Qcheck = $this->app->db->get('products_to_categories', 'categories_id', ['products_id' => (int)$id,
                                                                                      'categories_id' => (int)$value_id
                                                                                     ]
                                         );

           if ($Qcheck->fetch()) {
//move in other category
             if ($new_category == $current_category) {
              $Qupdate = $this->app->db->prepare('update :table_products_to_categories
                                                  set categories_id = :categories_id
                                                  where products_id = :products_id
                                                ');
              $Qupdate->bindInt(':products_id', (int)$id);
              $Qupdate->bindInt(':categories_id', (int)$value_id);
              $Qupdate->execute();
             }
           } else {
//if the product does not exist inside the category
              if ($value_id != $current_category) {
                $count = $this->productsAdmin->getCountProductsToCategory($id, $value_id);

                if ($count < 1) {
// just link the product another category
                  if ($products_link == 'link') {
                    if ($current_category != $value_id) {
                      $sql_array = ['products_id' => (int)$id,
                                    'categories_id' => (int)$value_id
                                   ];

                      $this->app->db->save('products_to_categories', $sql_array);
                    } else {
                      $CLICSHOPPING_MessageStack->add($this->app->getDef('error_cannot_link_to_same_category'), 'danger');
                    }
                  }

                  if ($products_link == 'duplicate') {
                    $this->productsAdmin->cloneProductsInOtherCategory($id, $value_id);
                  }
                }
              }
            }
          }
        }
      }

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');
    }

    public function execute() {
      if (!defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      $id =  HTML::sanitize($_GET['pID']);

      $this->UpdateProductCategories($id);
    }
  }
