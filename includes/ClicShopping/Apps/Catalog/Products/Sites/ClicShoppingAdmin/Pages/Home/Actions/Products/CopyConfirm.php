<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin;
use function is_null;

class CopyConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  protected mixed $app;
  protected $ID;
  protected $categoriesId;
  protected $currentCategoryId;
  protected $copyAs;
  protected $productsAdmin;

  public function __construct()
  {
    $this->app = Registry::get('Products');
    $this->messageStack = Registry::get('MessageStack');

    $this->ID = HTML::sanitize($_POST['products_id']);
    $this->categoriesId = HTML::sanitize($_POST['categories_id']);
    $this->currentCategoryId = HTML::sanitize($_POST['current_category_id']);
    $this->copyAs = $_POST['copy_as'];

    $this->productsAdmin = new ProductsAdmin();
  }

  /**
   *
   */
  private function Link(): void
  {
    if ($this->categoriesId != $this->currentCategoryId) {
      $new_category = $this->categoriesId;

      if (\is_array($new_category) && isset($new_category)) {
        foreach ($new_category as $value_id) {

          $update_array = [
            'products_id' => (int)$this->ID,
            'categories_id' => (int)$value_id
          ];

          $Qcheck = $this->app->db->get('products_to_categories', 'categories_id', $update_array);

          if ($Qcheck->fetch() === false) {
            if ($value_id != $this->currentCategoryId) {
              $count = $this->productsAdmin->getCountProductsToCategory($this->ID, $value_id);
              if ($count < 1) {
                $sql_array = ['products_id' => $this->ID,
                  'categories_id' => $value_id
                ];

                $this->app->db->save('products_to_categories', $sql_array);
              }
            }
          }
        }
      }
    }
  }

  /**
   *
   */
  private function productsDuplicate(): void
  {
    $new_category = $this->categoriesId;

    if (\is_array($new_category) && isset($new_category)) {
      foreach ($new_category as $value_id) {
        if ($this->copyAs == 'duplicate') {
          $this->productsAdmin->cloneProductsInOtherCategory($this->ID, $value_id);
        }
      }
    }
  }

  /**
   *
   */
  private function productsLink(): void
  {
    if ($this->copyAs == 'link') {
      if ($this->categoriesId != $this->currentCategoryId) {
        $this->Link();
      } else {
        $this->messageStack->add($this->app->getDef('error_cannot_link_to_same_category'), 'error');
      }
    }
  }

  public function execute()
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    if (isset($this->ID) && isset($this->categoriesId)) {
      $this->productsDuplicate();
      $this->productsLink();

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');

      $CLICSHOPPING_Hooks->call('Products', 'CopyConfirm');

      $this->messageStack->add($this->app->getDef('alert_message_b2b_update'), 'warning');

      $this->app->redirect('Products&cPath=' . $this->currentCategoryId . '&pID=' . $this->ID);
    }
  }
}