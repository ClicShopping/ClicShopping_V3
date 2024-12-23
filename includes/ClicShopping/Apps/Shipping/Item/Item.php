<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Shipping\Item;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Item extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Item_V1';

  /**
   * Initializes the necessary setup or configurations for the current instance.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves configuration modules from a specified directory and organizes them by sort order.
   * The method scans the directory for eligible configuration modules, validates their subclassing,
   * and returns a sorted array of module names.
   *
   * @return mixed Array of configuration module names sorted by their sort_order or an internal counter.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Shipping/Item/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Shipping\Item\Item::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve configuration for.
   * @param string $info The specific configuration or property to retrieve from the module.
   * @return mixed The requested configuration information or property associated with the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ItemAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
      Registry::set('ItemAdminConfig' . $module, new $class);
    }

    return Registry::get('ItemAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The version of the API, which can be either a string or an integer.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   *
   * @return string The identifier associated with the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
