<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class ProductsAttributes extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_ProductsAttributes_V1';

  /**
   * Initializes the required configurations or settings for the class instance.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules for the application.
   *
   * This method scans a specified directory for configuration module classes,
   * validates them as subclasses of a specific abstract class, and organizes them
   * based on their sort order or default index position. The result is cached
   * statically to avoid repeated directory scanning.
   *
   * @return mixed An associative array of configuration module names indexed by their sort order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Catalog/ProductsAttributes/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Catalog\ProductsAttributes\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Catalog\ProductsAttributes\ProductsAttributes::getConfigModules(): ';

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
   * Retrieves configuration module information.
   *
   * @param string $module The name of the module to load.
   * @param string $info The specific information or property to retrieve from the module.
   * @return mixed The requested information from the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ProductsAttributesAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Catalog\ProductsAttributes\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ProductsAttributesAdminConfig' . $module, new $class);
    }

    return Registry::get('ProductsAttributesAdminConfig' . $module)->$info;
  }

  /**
   *
   * @return string|int The current API version.
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
