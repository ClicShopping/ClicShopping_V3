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

class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
{
  private mixed $app;
  protected $selected;
  protected $cPath;

  public function __construct()
  {
    $this->app = Registry::get('Products');
    $this->cPath = HTML::sanitize($_GET['cPath']);
  }

  public function execute()
  {
    $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    if (isset($_POST['selected']) && is_array($_POST['selected'])) {
      foreach ($_POST['selected'] as $items) {
        if (isset($items)) {
          $Qcheck = $this->app->db->prepare('select count(*)
                                               from :table_products_to_categories
                                               where products_id = :products_id
                                              ');
          $Qcheck->bindInt(':products_id', (int)$items);
          $Qcheck->execute();

          if ($Qcheck->rowCount() > 0) {
            $CLICSHOPPING_ProductsAdmin->removeProduct($items);
            $CLICSHOPPING_Hooks->call('Products', 'DeleteAll');
          }
        }
      }
    }

    Cache::clear('categories');
    Cache::clear('products-also_purchased');
    Cache::clear('products_related');
    Cache::clear('products_cross_sell');
    Cache::clear('upcoming');

    $this->app->redirect('Products&cPath=' . $this->cPath);
  }
}