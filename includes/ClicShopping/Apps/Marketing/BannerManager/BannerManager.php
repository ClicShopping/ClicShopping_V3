<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\BannerManager;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class BannerManager extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_BannerManager_V1';

  /**
   * Initializes the required properties or components for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and returns an array of configuration module names for the application.
   * The method iterates through a specific directory to locate configuration modules,
   * ensures that they are valid subclasses of a defined ConfigAbstract class, and sorts them
   * based on their defined sort order or their natural order in absence of a sort_order.
   *
   * @return mixed An array of configuration module names indexed by their sort order or null if no configuration modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/BannerManager/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Marketing\BannerManager\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Marketing\BannerManager\BannerManager::getConfigModules(): ';

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
   * Retrieves specific configuration information for a given module.
   *
   * @param string $module The name of the module to retrieve the configuration for.
   * @param string $info The specific information or property to retrieve from the module's configuration.
   * @return mixed The requested configuration information or property value for the given module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('BannerManagerAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Marketing\BannerManager\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('BannerManagerAdminConfig' . $module, new $class);
    }

    return Registry::get('BannerManagerAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version as a string or integer.
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
