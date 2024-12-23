<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\TotalTax;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class TotalTax extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_TotalTax_V1';

  /**
   * Initializes the necessary components or configurations for the current context.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules from the specified directory.
   *
   * This method scans a predefined directory for configuration modules,
   * validating that they are subclasses of the required abstract base class.
   * If valid, the modules are sorted based on their sort order or the order in
   * which they are discovered.
   *
   * @return mixed An array of configuration module names indexed by their sort order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/OrderTotal/TotalTax/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\OrderTotal\TotalTax\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\OrderTotal\TotalTax\TotalTax::getConfigModules(): ';

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
   * Retrieves specific configuration module information.
   *
   * @param string $module The name of the module to retrieve the configuration for.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed The requested configuration information for the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('TotalTaxAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\OrderTotal\TotalTax\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
      Registry::set('TotalTaxAdminConfig' . $module, new $class);
    }

    return Registry::get('TotalTaxAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version, which can be either a string or an integer.
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
