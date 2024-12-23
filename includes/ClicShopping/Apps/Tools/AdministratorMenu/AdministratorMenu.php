<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\AdministratorMenu;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class AdministratorMenu extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_AdministratorMenu_V1';

  /**
   * Initializes the required setup or configuration for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and organizes the configuration modules from the specified directory.
   * The method checks the directory for valid configuration modules that are subclasses
   * of the defined `ConfigAbstract` class within the given namespace. Modules are
   * sorted by their respective sort orders; if none is provided, they are added sequentially.
   *
   * @return mixed An array of configuration module names sorted by their order,
   *               or an empty array if no valid modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/AdministratorMenu/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\AdministratorMenu\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\AdministratorMenu\AdministratorMenu::getConfigModules(): ';

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
   * Retrieves configuration module information based on the specified module and info parameters.
   *
   * @param string $module The name of the module to retrieve configuration for.
   * @param string $info The information field to be fetched from the module.
   * @return mixed Returns the requested configuration information from the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('AdministratorMenuAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\AdministratorMenu\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('AdministratorMenuAdminConfig' . $module, new $class);
    }

    return Registry::get('AdministratorMenuAdminConfig' . $module)->$info;
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
