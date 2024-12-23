<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Report\StatsProductsNotification;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class StatsProductsNotification extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_StatsProductsNotification_V1';

  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules available in the specified directory.
   *
   * This method scans the given directory for valid configuration modules that are
   * subclasses of the specified abstract configuration class. The modules are sorted
   * based on their "sort_order" property or, if not specified, their order of discovery.
   *
   * @return mixed An array of configuration module names sorted by their "sort_order".
   *               Returns an empty array if no valid modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Report/StatsProductsNotification/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Report\StatsProductsNotification\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Report\StatsProductsNotification\StatsProductsNotification::getConfigModules(): ';

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
   * Retrieves configuration module information based on the provided module and information key.
   *
   * @param string $module The module name whose configuration information is requested.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed Returns the corresponding configuration information for the provided module and key.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('StatsProductsNotificationAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Report\StatsProductsNotification\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('StatsProductsNotificationAdminConfig' . $module, new $class);
    }

    return Registry::get('StatsProductsNotificationAdminConfig' . $module)->$info;
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
   * Retrieves the identifier associated with the current instance.
   *
   * @return string The identifier as a string.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
