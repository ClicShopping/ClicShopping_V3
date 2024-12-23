<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\TotalShipping;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class TotalShipping extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_TotalShipping_V1';

  /**
   * Initializes the necessary configurations or settings for the class or object.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules for the specific directory, applying sorting based on the module's sort order.
   * The method ensures that the modules are subclasses of a specific configuration abstract class.
   *
   * @return mixed Returns an array of configuration module names sorted by their order, or an empty array if no valid modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/OrderTotal/TotalShipping/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\OrderTotal\TotalShipping\TotalShipping::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve configuration for.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed Returns the requested information from the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('TotalShippingAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
      Registry::set('TotalShippingAdminConfig' . $module, new $class);
    }

    return Registry::get('TotalShippingAdminConfig' . $module)->$info;
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
   * Retrieves the identifier.
   *
   * @return string The identifier value.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
