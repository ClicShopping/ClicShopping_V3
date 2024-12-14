<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;
use ClicShopping\Sites\Shop\RewriteUrl as RewriteUrlClass;
/**
 * The RewriteUrls service class is responsible for initializing URL rewriting functionality in the shop system.
 * It ensures that the required RewriteUrl class is loaded and registered with the Registry for use throughout the application.
 * It also integrates the service to execute specific logic before the page content is generated.
 */
class RewriteUrls implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Sites/Shop/RewriteUrl.php')) {
      $CLICSHOPPING_Service = Registry::get('Service');

      if (!Registry::exists('RewriteUrl')) {
        Registry::set('RewriteUrl', new RewriteUrlClass());
      }

      $CLICSHOPPING_Service->addCallBeforePageContent('Address', 'initialize');

      return true;
    } else {
      return false;
    }
  }

  public static function stop(): bool
  {
    return true;
  }
}
