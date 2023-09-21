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
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use ClicShopping\Sites\Shop\Breadcrumb as BreadcrumbClass;

class Breadcrumb implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Sites/Shop/Breadcrumb.php')) {
      Registry::set('Breadcrumb', new BreadcrumbClass());
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('header_title_top'), CLICSHOPPING::getConfig('http_server', 'Shop'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('header_title_catalog', ['store_name' => HTML::sanitize(STORE_NAME)]), CLICSHOPPING::link());

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
