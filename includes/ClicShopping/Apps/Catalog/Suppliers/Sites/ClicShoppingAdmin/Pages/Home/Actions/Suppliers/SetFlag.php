<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Catalog\Suppliers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Suppliers;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Suppliers\Classes\ClicShoppingAdmin\Status;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Suppliers');
  }

  public function execute()
  {
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_GET['id'], $_GET['flag'])) {
      Status::getSuppliersStatus($_GET['id'], $_GET['flag']);

      $this->app->redirect('Suppliers&page=' . $page . '&mID=' . (int)$_GET['id']);
    } else {
      $this->app->redirect('Suppliers&page=' . $page);
    }
  }
}