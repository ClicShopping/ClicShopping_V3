<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Customers;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Customers extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Customers_V1';

  /**
   * Initializes the necessary components or settings for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a sorted list of configuration module names from the specified directory.
   *
   * This method scans the given directory for configuration modules, validates whether
   * they are subclasses of the specified configuration abstract class, and sorts them
   * based on their defined sort order. If no sort order is defined, modules are appended
   * to the result list.
   *
   * @return mixed An array of configuration module names sorted by their defined order, or an empty array if none are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Customers/Customers/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Customers\Customers\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Customers\Customers\Customers::getConfigModules(): ';

      if ($dir = new \DirectoryIterator($directory)) {
        foreach ($dir as $file) {
          if (!$file->isDot() && $file->isDir() && is_file($file->getPathname() . DIRECTORY_SEPARATOR . $file->getFilename() . '.php')) {
            $class = '' . $name_space_config . '\\' . $file->getFilename() . '\\' . $file->getFilename();

            if (is_subclass_of($class, '' . $name_space_config . '\ConfigAbstract')) {
              $sort_order = $this->getConfigModuleInfo($file->getFilename(), 'sort_order');
              if ($sort_order > 0) {
                $counter = $sort_order;
              } else {
                $counter = count($result);
              }

              while (true) {
                if (isset($result[$counter])) {
                  $counter++;

                  continue;
                }

                $result[$counter] = $file->getFilename();

                break;
              }
            } else {
              trigger_error('' . $trigger_message . '' . $name_space_config . '\\' . $file->getFilename() . '\\' . $file->getFilename() . ' is not a subclass of ' . $name_space_config . '\ConfigAbstract and cannot be loaded.');
            }
          }

          ksort($result, SORT_NUMERIC);
        }
      }
    }

    return $result;
  }

  /**
   * Retrieves configuration module information for the specified module and info.
   *
   * @param string $module The name of the module to retrieve.
   * @param string $info The specific information to obtain from the module.
   * @return mixed Returns the requested information from the configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('CustomersAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Customers\Customers\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('CustomersAdminConfig' . $module, new $class);
    }

    return Registry::get('CustomersAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the API version.
   *
   * @return string|int The version of the API.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier associated with the current instance.
   *
   * @return string The identifier of the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
