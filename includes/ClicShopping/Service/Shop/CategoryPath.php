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

use ClicShopping\Apps\Catalog\Categories\Classes\Shop\Category as CategoryClass;
use ClicShopping\Apps\Catalog\Categories\Classes\Shop\CategoryTree as CategoryTreeClass;
/**
 * This service is used to manage the initialization of CategoryTree and Category classes
 * within the ClicShopping framework. It ensures these components are properly registered
 * and available for use in the application.
 */
class CategoryPath implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the category-related classes if the required file exists.
   *
   * @return bool Returns true if the file is found and categories are successfully initialized; otherwise, false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Categories/Classes/Shop/Category.php')) {
      Registry::set('CategoryTree', new CategoryTreeClass());
      Registry::set('Category', new CategoryClass());

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the requested process or operation.
   *
   * @return bool Returns true indicating the process has been successfully stopped.
   */
  public static function stop(): bool
  {
    return true;
  }
}