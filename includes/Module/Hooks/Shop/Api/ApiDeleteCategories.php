<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Api;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use function count;

use ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin\CategoriesAdmin;

class ApiDeleteCategories
{
  /**
   * @param int $id
   * @return void
   */
  private static function deleteCategories(int $id): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    Registry::set('CategoriesAdmin', new CategoriesAdmin());
    $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

    $categories_id = $id;
    $cPath = 0;

    if (isset($categories_id) && is_numeric($categories_id) && isset($cPath)) {
      $categories = $CLICSHOPPING_CategoriesAdmin->getCategoryTree($categories_id, '', '0', '', true);
      $products = [];
      $products_delete = [];

      for ($i = 0, $n = count($categories); $i < $n; $i++) {
        $QproductIds = $CLICSHOPPING_Db->get('products_to_categories', 'products_id', ['categories_id' => (int)$categories[$i]['id']]);

        while ($QproductIds->fetch()) {
          $products[$QproductIds->valueInt('products_id')]['categories'][] = $categories[$i]['id'];
        }
      }

      foreach ($products as $key => $value) {
        $category_ids = '';

        for ($i = 0, $n = count($value['categories']); $i < $n; $i++) {
          $category_ids .= "'" . (int)$value['categories'][$i] . "', ";
        }

        $category_ids = substr($category_ids, 0, -2);

        $Qcheck = $CLICSHOPPING_Db->prepare('select products_id
                                              from :table_products_to_categories
                                              where products_id = :products_id
                                              and categories_id not in (' . $category_ids . ')
                                              limit 1
                                              ');

        $Qcheck->bindInt(':products_id', $key);
        $Qcheck->execute();

        if ($Qcheck->check() === false) {
          $products_delete[$key] = $key;
        }
      }

      for ($i = 0, $n = count($categories); $i < $n; $i++) {
        $CLICSHOPPING_CategoriesAdmin->removeCategory($categories[$i]['id']);
      }

      foreach (array_keys($products_delete) as $key) {
        $CLICSHOPPING_Hooks->call('Products', 'RemoveProduct');
      }

      $CLICSHOPPING_Hooks->call('Categories', 'DeleteConfirm');

      Cache::clear('category_tree-');
      Cache::clear('also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');
    }
  }

  public function execute()
  {
    if (isset($_GET['cId'], $_GET['categories'])) {
      $id = HTML::sanitize($_GET['cId']);

      if (!is_numeric($id)) {
        http_response_code(400);
        return json_encode(['error' => 'Invalid ID format']);
      }

      static::deleteCategories($id);
    } else {
      return false;
    }
  }
}