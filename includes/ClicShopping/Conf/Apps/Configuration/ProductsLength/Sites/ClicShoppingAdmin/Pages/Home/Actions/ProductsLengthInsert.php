<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsLength\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class ProductsLengthInsert extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_ProductsLength = Registry::get('ProductsLength');

    $this->page->setFile('products_length_insert.php');
    $this->page->data['action'] = 'ProductsLengthInsert';

    $CLICSHOPPING_ProductsLength->loadDefinitions('Sites/products_length');
  }
}