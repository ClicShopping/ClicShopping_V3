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

use ClicShopping\Apps\Catalog\Categories\Classes\Common\CategoryCommon as CategoryCommonClass;
/**
 * Service class responsible for managing the initialization and shutdown of
 * the CategoriesCommon functionality within the ClicShopping system. It registers
 * the CategoryCommon class into the Registry for use across the application.
 */
class CategoriesCommon implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Starts the process by checking the existence of a specific file.
   * If the file exists, it initializes the CategoryCommonClass and
   * registers it under the 'CategoryCommon' key in the Registry.
   *
   * @return bool Returns true if the file exists and the process is initialized successfully, otherwise false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Categories/Classes/Common/CategoryCommon.php')) {
      Registry::set('CategoryCommon', new CategoryCommonClass());

      return true;
    } else {
      return false;
    }
  }

  /**
   * Terminates a process or operation.
   *
   * @return bool Returns true indicating the successful termination of the process or operation.
   */
  public static function stop(): bool
  {
    return true;
  }
}
