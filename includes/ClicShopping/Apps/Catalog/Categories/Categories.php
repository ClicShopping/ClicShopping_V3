<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Categories;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

/**
 * Handles operations related to the Categories application module.
 * Provides functionalities like retrieving configuration modules, fetching
 * configuration module information, and accessing metadata such as API versions
 * and identifiers. This class extends the AppAbstract base class to ensure
 * consistency and shared functionalities across application modules.
 */
class Categories extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Categories_V1';

  /**
   * Initializes the necessary configurations or setups required by the implementing class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a sorted list of configuration modules available in a specific directory.
   *
   * This method scans a predefined directory for subdirectories containing configuration modules.
   * Each valid configuration module must be a subclass of ConfigAbstract
   * and comply with a specific namespace structure. The modules are sorted based on their
   * sort order retrieved from their metadata or in the order they were processed.
   *
   * @return mixed The sorted list of configuration modules. If no valid modules are found, an empty array is returned.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Categories/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Catalog\Categories\Categories::getConfigModules(): ';

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
   * Retrieves configuration module information based on the provided module and information key.
   *
   * @param string $module The name of the module for which configuration information is requested.
   * @param string $info The specific information key within the module to retrieve.
   * @return mixed Returns the requested configuration information or null if not available.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('CategoriesAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('CategoriesAdminConfig' . $module, new $class);
    }

    return Registry::get('CategoriesAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the version of the API.
   *
   * @return string|int The API version.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   *
   * @return string The identifier associated with the object.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
