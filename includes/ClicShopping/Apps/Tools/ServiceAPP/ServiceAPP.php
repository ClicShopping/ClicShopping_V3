<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\ServiceAPP;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class ServiceAPP extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_ServiceAPP_V1';

  /**
   * Initializes the instance or prepares the required setup.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and sorts configuration modules from the specified directory.
   *
   * This method iterates over the configuration modules located in a predefined directory
   * and organizes them based on their `sort_order` property. It ensures that only the modules
   * extending the designated abstract configuration class are included. If no `sort_order` is specified,
   * it uses the current count of the result array for ordering. The result is cached statically
   * for subsequent invocations to avoid redundant computations.
   *
   * @return mixed An array of configuration module names indexed by their respective sort order,
   *               or an error if any module does not conform to requirements.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/ServiceAPP/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\ServiceAPP\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\ServiceAPP\ServiceAPP::getConfigModules(): ';

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
   * Retrieves configuration module information for a specific module and info key.
   *
   * @param string $module The name of the module whose configuration information is to be retrieved.
   * @param string $info The specific configuration information to retrieve from the module.
   * @return mixed Returns the requested configuration information for the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ServiceAPPAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\ServiceAPP\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ServiceAPPAdminConfig' . $module, new $class);
    }

    return Registry::get('ServiceAPPAdminConfig' . $module)->$info;
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
   *
   * @return string The identifier associated with the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
