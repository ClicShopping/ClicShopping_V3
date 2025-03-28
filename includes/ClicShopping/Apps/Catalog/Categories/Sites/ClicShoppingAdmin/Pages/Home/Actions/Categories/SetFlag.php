<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Categories\Sites\ClicShoppingAdmin\Pages\Home\Actions\Categories;

use ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin\Status;
use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Categories');
  }

  public function execute()
  {
    if (($_GET['flag'] == 0) || ($_GET['flag'] == 1)) {
      if (isset($_GET['cPath'])) {
        $cPath = HTML::sanitize($_GET['cPath']);
      } else {
        $cPath = 0;
      }

      if (isset($_GET['cID'])) {
        Status::getCategoriesStatus($_GET['cID'], (int)$_GET['flag']);
      }

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');
    }

    if (isset($_GET['cID'])) {
      $this->app->redirect('Categories&cPath=' . $cPath . '&cID=' . $_GET['cID']);
    } else {
      $this->app->redirect('Categories&cPath=' . $cPath);
    }
  }
}