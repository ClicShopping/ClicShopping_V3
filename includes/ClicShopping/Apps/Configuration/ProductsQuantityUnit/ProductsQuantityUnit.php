<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class ProductsQuantityUnit extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_ProductsQuantityUnit_V1';

  /**
   * Initializes the necessary components or configurations for the object.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules sorted by their specified order.
   * The method scans the defined directory for modules that implement a specific
   * configuration class and organizes them into a sorted array.
   *
   * @return mixed Returns an array of configuration module filenames, sorted by their sort order.
   *               If no modules are found or if an issue arises, it returns an empty array.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/ProductsQuantityUnit/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\ProductsQuantityUnit\ProductsQuantityUnit::getConfigModules(): ';

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
   * Retrieves configuration module information.
   *
   * @param string $module The name of the module to retrieve the information from.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed The requested configuration information.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ProductsQuantityUnitAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\ProductsQuantityUnit\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ProductsQuantityUnitAdminConfig' . $module, new $class);
    }

    return Registry::get('ProductsQuantityUnitAdminConfig' . $module)->$info;
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
   * @return string Returns the identifier.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
