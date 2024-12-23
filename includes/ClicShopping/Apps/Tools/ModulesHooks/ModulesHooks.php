<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\ModulesHooks;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class ModulesHooks extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_ModulesHooks_V1';

  /**
   * Initializes the necessary configurations or components for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and caches the configuration modules available in a specific directory.
   *
   * This method scans a designated directory for configuration module subdirectories,
   * validates them as subclasses of a defined abstract class, and orders them based on
   * their sort order or insertion sequence.
   *
   * @return mixed Returns an array of configuration module names, indexed by their sort order, or null if the directory could not be processed.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/ModulesHooks/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\ModulesHooks\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\ModulesHooks\ModulesHooks::getConfigModules(): ';

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
   * Retrieves the configuration module information based on the module name and requested information.
   *
   * @param string $module The name of the module to retrieve information for.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed The requested module information, or null if it does not exist.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ModulesHooksAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\ModulesHooks\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ModulesHooksAdminConfig' . $module, new $class);
    }

    return Registry::get('ModulesHooksAdminConfig' . $module)->$info;
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
   *
   * @return string The identifier of the current instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
