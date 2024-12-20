<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Suppliers extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Suppliers_V1';

  /**
   * Initializes the necessary components or configurations for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and organizes configuration modules available in the specified directory.
   *
   * @return mixed Returns an array of configuration module names sorted by their sort order, or an empty array if no modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Suppliers/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Catalog\Suppliers\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Catalog\Suppliers\Suppliers::getConfigModules(): ';

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
   * Retrieves configuration module information based on the specified module and information key.
   *
   * @param string $module The name of the configuration module to retrieve.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed The requested information from the configuration module, or null if unavailable.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('SuppliersAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Catalog\Suppliers\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('SuppliersAdminConfig' . $module, new $class);
    }

    return Registry::get('SuppliersAdminConfig' . $module)->$info;
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
   *
   * @return string The identifier of the current object.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
