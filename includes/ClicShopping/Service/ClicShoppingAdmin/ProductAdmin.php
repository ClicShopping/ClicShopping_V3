<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ProductsAdmin as ProductAdminClass;

class ProductAdmin implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    Registry::set('ProductsAdmin', new ProductAdminClass());

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
