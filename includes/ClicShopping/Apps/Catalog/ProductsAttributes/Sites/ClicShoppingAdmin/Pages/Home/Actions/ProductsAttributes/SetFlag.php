<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsAttributes;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\ProductsAttributes\Classes\ClicShoppingAdmin\ProductsAttributesStatusAdmin;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  private mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('ProductsAttributes');
  }

  public function execute()
  {
    if (($_GET['flag'] == 0) || ($_GET['flag'] == 1)) {
      if (isset($_GET['products_attributes_id'])) {
        ProductsAttributesStatusAdmin::getStatus($_GET['products_attributes_id'], $_GET['flag']);
      }
    }

    if (isset($_GET['products_attributes_id'])) {
      $this->app->redirect('ProductsAttributes&products_attributes_id=' . $_GET['products_attributes_id'] . '#tab3');
    } else {
      $this->app->redirect('ProductsAttributes#tab3');
    }
  }
}