<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxRates;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class TaxRates extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_TaxRates_V1';

  /**
   * Initializes the necessary configurations or settings for the class instance.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and returns the configuration modules available in the specified directory.
   * The method scans the directory for valid configuration modules, verifies if they are subclasses
   * of the required abstract class, organizes them by sort order, and returns the sorted list.
   *
   * @return mixed An array of configuration modules sorted by order, or empty array if none are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/TaxRates/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\TaxRates\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\TaxRates\TaxRates::getConfigModules(): ';

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
   * Retrieves configuration module information for a specified module and info key.
   *
   * @param string $module The name of the configuration module.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed The requested information from the specified configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('TaxRatesAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\TaxRates\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('TaxRatesAdminConfig' . $module, new $class);
    }

    return Registry::get('TaxRatesAdminConfig' . $module)->$info;
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
