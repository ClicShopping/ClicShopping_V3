<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecDirPermissions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class SecDirPermissions extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_SecDirPermissions_V1';

  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules available in the specified directory.
   *
   * The method scans a predefined directory for module files, validates if they
   * extend the necessary abstract class, and organizes them by their sort order.
   * If the sort order is not specified in the module, they are assigned a sort
   * position sequentially.
   *
   * @return array An associative array of configuration modules where the keys
   *               represent their sort order, and the values represent module names.
   */
  public function getConfigModules()
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/SecDirPermissions/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\SecDirPermissions\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\SecDirPermissions\SecDirPermissions::getConfigModules(): ';

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
   * Retrieves configuration module information by initializing the specified module if not already registered.
   *
   * @param string $module The name of the configuration module to fetch information from.
   * @param string $info The specific information required from the configuration module.
   * @return mixed Returns the requested configuration module information.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('SecDirPermissionsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\SecDirPermissions\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('SecDirPermissionsAdminConfig' . $module, new $class);
    }

    return Registry::get('SecDirPermissionsAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version, which can be a string or an integer.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier associated with the instance.
   *
   * @return string The identifier value.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
