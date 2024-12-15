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

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin\CategoriesAdmin as CategoriesAdminClass;

/**
 * Service class responsible for initializing the CategoriesAdmin module
 * within the ClicShoppingAdmin environment. This class implements the
 * ServiceInterface and handles the starting and stopping of the service.
 */
class CategoriesAdmin implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the CategoriesAdmin class by checking for the existence of the required file
   * and registering it in the application registry.
   *
   * @return bool Returns true if the file exists and the class is successfully registered, otherwise false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Categories/Classes/ClicShoppingAdmin/CategoriesAdmin.php')) {
      Registry::set('CategoriesAdmin', new CategoriesAdminClass());

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the current process or operation.
   *
   * @return bool Returns true indicating the process stopped successfully.
   */
  public static function stop(): bool
  {
    return true;
  }
}
