<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\SecurityCheck;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class SecurityCheck extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_SecurityCheck_V1';

  /**
   * Initializes the necessary components or configuration for the current instance.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules sorted by their sort order.
   * If the sort order is not defined, the modules are appended at the end.
   *
   * @return mixed Returns an array of configuration module names, sorted by their defined order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/SecurityCheck/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\SecurityCheck\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\SecurityCheck\SecurityCheck::getConfigModules(): ';

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
   * @param string $module The name of the module for which the configuration information is requested.
   * @param string $info The specific information or property to retrieve from the module's configuration.
   * @return mixed The value of the requested configuration information.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('SecurityCheckAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\SecurityCheck\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('SecurityCheckAdminConfig' . $module, new $class);
    }

    return Registry::get('SecurityCheckAdminConfig' . $module)->$info;
  }

  /**
   *
   * @return string|int Returns the API version, which can be either a string or an integer.
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
