<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Groups extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Reviews_V1';

  /**
   * Initializes the necessary properties or settings for the class or component.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and initializes the list of configuration modules for a specific directory
   * and namespace. The modules must be subclasses of the specified `ConfigAbstract`
   * class, and they are sorted based on their sort order if available.
   *
   * @return mixed Returns an array containing the configuration modules, keyed by their
   * sort order. If no modules are found, an empty array is returned.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Customers/Groups/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Customers\Groups\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Customers\Groups\Groups::getConfigModules(): ';

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
   *
   * Retrieves configuration module information based on the specified module and information key.
   *
   * @param string $module The name of the module whose configuration information is to be retrieved.
   * @param string $info The specific information key to fetch from the module's configuration.
   * @return mixed Returns the requested configuration information for the given module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('GroupsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Customers\Groups\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('GroupsAdminConfig' . $module, new $class);
    }

    return Registry::get('GroupsAdminConfig' . $module)->$info;
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
   * @return string The identifier.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
