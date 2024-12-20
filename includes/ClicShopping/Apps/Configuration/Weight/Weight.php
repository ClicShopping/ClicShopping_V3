<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Weight;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Weight extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Weight_V1';

  /**
   * Initializes the required components or properties for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and caches configuration modules from a specified directory.
   *
   * This method iterates through a defined directory to locate and load configuration modules
   * if they adhere to the expected class structure. Modules are sorted based on their
   * 'sort_order' or their order of discovery in the absence of a specific sort order.
   *
   * @return mixed An array of configuration module names, sorted by their 'sort_order' value. Returns an empty array if no valid modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Weight/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\Weight\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\Weight\Weight::getConfigModules(): ';

      if ($dir = new \DirectoryIterator($directory)) {
        foreach ($dir as $file) {
          if (!$file->isDot() && $file->isDir() && is_file($file->getPathname() . '/' . $file->getFilename() . '.php')) {
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
   * Retrieves specific configuration module information.
   *
   * @param string $module The name of the module to retrieve configuration information from.
   * @param string $info The specific information or property to retrieve from the module.
   * @return mixed The requested configuration information from the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('WeightAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\Weight\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('WeightAdminConfig' . $module, new $class);
    }

    return Registry::get('WeightAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version.
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
