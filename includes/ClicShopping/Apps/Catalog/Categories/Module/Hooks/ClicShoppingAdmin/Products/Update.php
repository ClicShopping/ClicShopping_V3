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

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin;

  class Update implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected ProductsAdmin $productsAdmin;
    protected string $productsLink;
    protected string $currentCategoryId;

    public function __construct()
    {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new CategoriesApp());
      }

      $this->app = Registry::get('Categories');

      if (isset($_POST['copy_as'])) {
        $this->productsLink = HTML::sanitize($_POST['copy_as']);
      } else {
        $this->productsLink = 'none';
      }

      $this->currentCategoryId = HTML::sanitize($_POST['cPath']);

      $this->productsAdmin = new ProductsAdmin();
    }

    public function moveCategory($move_new_category, $id)
    {
      $QCheck = $this->app->db->prepare('select count(*)
                                          from :table_products_to_categories
                                          where products_id = :products_id
                                          and categories_id not in ( :categories_id )
                                        ');
      $QCheck->bindInt(':products_id', $id);
      $QCheck->bindInt(':categories_id', $move_new_category);
      $QCheck->execute();

      if ($QCheck->rowCount() > 0) {
        $Qupdate = $this->app->db->prepare('update :table_products_to_categories
                                            set categories_id = :categories_id
                                            where products_id = :products_id
                                            and categories_id = :categories_id1
                                          ');
        $Qupdate->bindInt(':categories_id', $move_new_category);
        $Qupdate->bindInt(':products_id', $id);
        $Qupdate->bindInt(':categories_id1', $this->currentCategoryId);

        $Qupdate->execute();
      }
    }

    public function updateProductCategories($id = null)
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_GET['Update'])) {
        if (isset($_POST['move_to_category_id'])) {
          $new_category = HTML::sanitize($_POST['move_to_category_id']);
        } else {
          $new_category = null;
        }

        if (empty($this->productsLink) || $this->productsLink == 'move') {
          $move_new_category = $new_category[0];

          $this->moveCategory($move_new_category, $id);
        } elseif ($this->productsLink != 'move') {
//link the category
          if (\is_array($new_category) && isset($new_category)) {
            foreach ($new_category as $value_id) {
              $Qcheck = $this->app->db->get('products_to_categories', 'categories_id', ['products_id' => (int)$id,
                  'categories_id' => (int)$value_id
                ]
              );

              if ($Qcheck->fetch() === false) {
//if the product does not exist inside the category
                if ($value_id != $this->currentCategoryId) {
                  $count = $this->productsAdmin->getCountProductsToCategory($id, $value_id);

                  if ($count < 1) {
// just link the product another category
                    if ($this->productsLink == 'link') {
                      if ($this->currentCategoryId != $value_id) {
                        $sql_array = [
                          'products_id' => (int)$id,
                          'categories_id' => (int)$value_id
                        ];

                        $this->app->db->save('products_to_categories', $sql_array);
                      } else {
                        $CLICSHOPPING_MessageStack->add($this->app->getDef('error_cannot_link_to_same_category'), 'error');
                      }
                    }
                  }
                }

                if ($this->productsLink == 'duplicate') {
                  $this->productsAdmin->cloneProductsInOtherCategory($id, $value_id);
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

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['pID'])) {
        $id = HTML::sanitize($_GET['pID']);

        $this->updateProductCategories($id);
      }
    }
  }
