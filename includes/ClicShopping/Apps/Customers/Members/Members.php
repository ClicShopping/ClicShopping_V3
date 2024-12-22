<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Members;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Members extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Members_V1';

  /**
   * Initializes the component or performs necessary setup operations.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules from a specified directory and returns them as an ordered array.
   *
   * This method dynamically loads configuration modules that are subclasses of the designated ConfigAbstract
   * class and organizes them based on their sort order or natural order if no sort order is provided.
   *
   * @return mixed Returns an array of configuration module identifiers sorted by their respective order,
   *               or null if no configuration modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Customers/Members/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Customers\Members\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Customers\Members\Members::getConfigModules(): ';

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
   * Retrieves configuration module information based on the specified module and info parameters.
   *
   * @param string $module The name of the configuration module to retrieve.
   * @param string $info The specific information or property to access for the module.
   * @return mixed The requested configuration module information or property value.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('MembersAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Customers\Members\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('MembersAdminConfig' . $module, new $class);
    }

    return Registry::get('MembersAdminConfig' . $module)->$info;
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
   * Retrieves the identifier value.
   *
   * @return string The identifier associated with the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
