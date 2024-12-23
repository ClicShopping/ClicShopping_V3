<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Apps;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Apps extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Apps_V1';

  /**
   * Initializes the necessary components or configuration for the object.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules located in a specified directory and organizes them
   * based on their sort order or natural order.
   *
   * This method scans a predefined directory for configuration modules, validates whether
   * they inherit from the required abstract class, and arranges them accordingly. If the modules
   * are not properly subclassed or if errors occur, appropriate errors are triggered.
   *
   * @return mixed An array of configuration module names sorted by their sort order or natural order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/Apps/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\Apps\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\Apps\Apps::getConfigModules(): ';

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
   * Retrieves specific configuration information for a module.
   *
   * @param string $module The name of the module for which the configuration information is retrieved.
   * @param string $info The specific information to retrieve from the module's configuration.
   * @return mixed The requested configuration data of the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('AppsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\Apps\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('AppsAdminConfig' . $module, new $class);
    }

    return Registry::get('AppsAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the API version.
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
   * @return string The identifier.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
