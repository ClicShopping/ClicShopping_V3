<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Products extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Products_V1';

  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules for the application.
   * The modules are sorted based on their defined sort order or their order of appearance.
   * Only modules that subclass the specified ConfigAbstract class are considered valid.
   * If a module does not meet this requirement, an error is triggered.
   *
   * @return mixed Returns an array of configuration modules, keyed by their sort order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Products/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Catalog\Products\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Catalog\Products\Products::getConfigModules(): ';

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
   * Retrieves specific configuration information for the given module.
   *
   * @param string $module The name of the module to retrieve configuration for.
   * @param string $info The specific information or property to access within the module.
   * @return mixed The requested configuration information for the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ProductsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Catalog\Products\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ProductsAdminConfig' . $module, new $class);
    }

    return Registry::get('ProductsAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The version of the API.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier associated with the current instance.
   *
   * @return string The identifier of the current instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
