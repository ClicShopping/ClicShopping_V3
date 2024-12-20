<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Settings;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Settings extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Settings_V1';

  /**
   * Initializes the necessary settings or configurations required for the object or process.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules within a specific directory.
   *
   * The method scans a predefined directory for subdirectories containing
   * configuration module definitions. It validates whether each module
   * is a subclass of a specified abstract class and organizes them
   * based on their sort order or directory structure.
   *
   * @return mixed Returns an array of configuration module names ordered
   *               numerically, or an empty array if no valid modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Settings/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\Settings\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\Settings\Settings::getConfigModules(): ';

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
   * Retrieves specific information of a configuration module.
   *
   * @param string $module The name of the configuration module.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed Returns the requested information from the configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('SettingsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\Settings\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('SettingsAdminConfig' . $module, new $class);
    }

    return Registry::get('SettingsAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the version of the API.
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
