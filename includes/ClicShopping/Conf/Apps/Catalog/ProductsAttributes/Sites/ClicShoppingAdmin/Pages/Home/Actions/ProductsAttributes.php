<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class ProductsAttributes extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

    $this->page->setFile('products_attributes.php');
    $this->page->data['action'] = 'ProductsAttributes';

    $CLICSHOPPING_ProductsAttributes->loadDefinitions('Sites/ClicShoppingAdmin/products_attributes');
  }
}