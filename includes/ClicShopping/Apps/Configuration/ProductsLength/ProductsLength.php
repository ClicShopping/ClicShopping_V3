<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsLength;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class ProductsLength extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_ProductsLength_V1';

  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules available in the specified directory.
   *
   * This method scans the designated directory for configuration modules that are subclasses of the
   * specified `ConfigAbstract` class. Modules are organized by their `sort_order` property or, if
   * undefined, appended sequentially. The result is cached for subsequent calls.
   *
   * @return mixed Returns an array of configuration module names, sorted based on their designated order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/ProductsLength/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\ProductsLength\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\ProductsLength\ProductsLength::getConfigModules(): ';

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
   * Retrieves specific information about a configuration module.
   *
   * @param string $module The name of the configuration module to retrieve information for.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed The requested information from the specified configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ProductsLengthAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\ProductsLength\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ProductsLengthAdminConfig' . $module, new $class);
    }

    return Registry::get('ProductsLengthAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the API version.
   *
   * @return string|int The current API version.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier associated with the current instance.
   *
   * @return string The identifier.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
