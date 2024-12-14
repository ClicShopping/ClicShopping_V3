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

use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\Image as ImageClass;
/**
 * Service class for handling the initialization and termination of the Image component
 * in the ClicShoppingAdmin context.
 *
 * This service class integrates the Image functionality by registering it into the
 * application registry.
 */
class Image implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    Registry::set('Image', new ImageClass());

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
