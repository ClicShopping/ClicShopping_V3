<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxClass;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class TaxClass extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_TaxClass_V1';

  /**
   * Initializes the necessary components or configurations required for the method's functionality.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves configuration modules for the application by scanning the specified directory
   * and loading modules that are subclasses of a predefined abstract class.
   *
   * @return mixed An array containing the configuration module names sorted by their order,
   *               or an empty array if no valid modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/TaxClass/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\TaxClass\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\TaxClass\TaxClass::getConfigModules(): ';

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
   *
   * @param string $module The name of the module for which configuration information is being retrieved.
   * @param string $info The specific information or property to fetch from the module's configuration.
   * @return mixed Returns the requested configuration information for the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('TaxClassAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\TaxClass\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('TaxClassAdminConfig' . $module, new $class);
    }

    return Registry::get('TaxClassAdminConfig' . $module)->$info;
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
   * Retrieves the identifier.
   *
   * @return string The identifier value.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
