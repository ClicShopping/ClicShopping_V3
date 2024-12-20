<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Modules;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Modules extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Modules_V1';

  /**
   * Initializes the necessary settings or configurations for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the list of configuration modules available in the specified directory.
   *
   * This method initializes a static variable to cache the result, which consists of
   * an array of module names sorted by their defined sort order. If no sort order is
   * defined, the modules are added in an incremental manner. It iterates through the
   * specified directory, identifying valid subdirectories representing modules, and
   * validates that each module adheres to the required class structure. Modules that
   * fail this validation trigger an error.
   *
   * @return mixed An array of module names sorted by their sort order, or an empty array if no modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Modules/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\Modules\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\Modules\Modules::getConfigModules(): ';

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
   * Retrieves specific configuration information for a given module.
   *
   * @param string $module The name of the module for which configuration information is requested.
   * @param string $info The specific configuration information to retrieve.
   *
   * @return mixed The requested configuration information for the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ModulesAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\Modules\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ModulesAdminConfig' . $module, new $class);
    }

    return Registry::get('ModulesAdminConfig' . $module)->$info;
  }

  /**
   *
   * @return string|int Returns the API version.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier value.
   *
   * @return string The identifier.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
