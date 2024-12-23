<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\DataBaseTables;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class DataBaseTables extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_DataBaseTables_V1';

  /**
   * Initializes the necessary configurations or setups required for the function or class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules available in the specified directory.
   *
   * The method scans a predefined directory for valid configuration modules. Each module must
   * be a subclass of ConfigAbstract and adhere to the required naming conventions. Modules
   * are sorted based on their defined sort order or their position in the resulting list.
   *
   * @return mixed An array of configuration module names sorted by their sort order, or
   *         an empty array if no modules are found or an error occurs.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/DataBaseTables/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\DataBaseTables\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\DataBaseTables\DataBaseTables::getConfigModules(): ';

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
   * Retrieves configuration module information based on the provided module and info parameters.
   *
   * @param string $module The name of the module to retrieve configuration information for.
   * @param string $info The specific information or property to retrieve from the configuration module.
   * @return mixed Returns the requested configuration information or property.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('DataBaseTablesAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\DataBaseTables\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('DataBaseTablesAdminConfig' . $module, new $class);
    }

    return Registry::get('DataBaseTablesAdminConfig' . $module)->$info;
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
   * Retrieves the identifier.
   *
   * @return string The identifier value.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
