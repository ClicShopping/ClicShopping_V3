<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Administrators;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Administrators extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Administrators_V1';

  /**
   * Initializes the required components or configurations for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules from a specified directory.
   *
   * This method scans a directory for valid configuration modules,
   * verifies if they are subclasses of the designated abstract class,
   * and orders them based on their sort order or entry sequence.
   *
   * @return mixed Returns an array of configuration module names sorted by their order,
   *               or null if no modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Administrators/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\Administrators\Administrators::getConfigModules(): ';

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
   * Retrieves configuration information for a specified module.
   *
   * @param string $module The name of the module to retrieve information for.
   * @param string $info The specific information or property to retrieve from the module.
   * @return mixed The requested information or property from the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('AdministratorsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('AdministratorsAdminConfig' . $module, new $class);
    }

    return Registry::get('AdministratorsAdminConfig' . $module)->$info;
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
