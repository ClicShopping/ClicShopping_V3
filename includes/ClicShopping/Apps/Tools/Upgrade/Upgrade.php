<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Upgrade extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Upgrade_V1';

  /**
   * Initializes the required properties or configurations for the current class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules from a specific directory.
   *
   * This method scans a specified directory for config module files, verifies
   * that they are subclasses of a defined abstract class, and then organizes
   * them based on their specified or default sort order. If a module does not
   * meet the required criteria, a trigger error is produced.
   *
   * @return mixed Returns an array of config module names sorted by their sort order,
   *               or null if no modules are available.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/Upgrade/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\Upgrade\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\Upgrade\Upgrade::getConfigModules(): ';

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
   * Retrieves configuration module information based on the provided module and info keys.
   *
   * @param string $module The name of the module to retrieve configuration details for.
   * @param string $info The specific information or property to be retrieved from the module.
   * @return mixed Returns the specified configuration module information.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('UpgradeAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\Upgrade\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('UpgradeAdminConfig' . $module, new $class);
    }

    return Registry::get('UpgradeAdminConfig' . $module)->$info;
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
   * @return string The identifier.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
