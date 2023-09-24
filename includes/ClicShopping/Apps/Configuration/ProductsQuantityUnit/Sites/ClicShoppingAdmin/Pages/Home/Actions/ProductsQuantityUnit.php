<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class ProductsQuantityUnit extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_ProductsQuantityUnit = Registry::get('ProductsQuantityUnit');

    $this->page->setFile('products_quantity_unit.php');
    $this->page->data['action'] = 'ProductsQuantityUnit';

    $CLICSHOPPING_ProductsQuantityUnit->loadDefinitions('Sites/ClicShoppingAdmin/ProductsQuantityUnit');
  }
}