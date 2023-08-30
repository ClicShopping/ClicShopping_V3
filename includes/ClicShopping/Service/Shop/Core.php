<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\Shop;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Customers\Classes\Shop\CustomerShop as CustomerClass;
use ClicShopping\Sites\Shop\Tax as TaxClass;
use ClicShopping\Sites\Shop\NavigationHistory as NavigationHistoryClass;
use ClicShopping\Sites\Shop\ShoppingCart as ShoppingCartClass;
use ClicShopping\Sites\Shop\OrderTotal as OrderTotalClass;
use ClicShopping\Apps\Catalog\Products\Classes\Shop\Prod as ProdClass;
use ClicShopping\Apps\Catalog\Products\Classes\Shop\ProductsCommon as ProductsCommonClass;
use ClicShopping\Apps\Catalog\Products\Classes\Shop\ProductsFunctionTemplate as ProductsFunctionTemplateClass;
use ClicShopping\Apps\Catalog\ProductsAttributes\Classes\Shop\ProductsAttributesShop;

class Core implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    Registry::set('Customer', new CustomerClass());
    Registry::set('Tax', new TaxClass());
    Registry::set('Prod', new ProdClass());
    Registry::set('ProductsCommon', new ProductsCommonClass());
    Registry::set('ProductsFunctionTemplate', new ProductsFunctionTemplateClass());
    Registry::set('OrderTotal', new OrderTotalClass());
    Registry::set('ProductsAttributes', new ProductsAttributesShop());

    if (!isset($_SESSION['cart']) || !\is_object($_SESSION['cart']) || (\get_class($_SESSION['cart']) !== 'shoppingCart')) {
      Registry::set('ShoppingCart', new ShoppingCartClass());
    }

    Registry::set('NavigationHistory', new NavigationHistoryClass(true));

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
