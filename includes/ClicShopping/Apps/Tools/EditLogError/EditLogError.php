<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditLogError;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class EditLogError extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_EditLogError_V1';

  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules available within the specified directory.
   *
   * The method scans a predefined directory for configuration module directories,
   * checks if they meet the required conditions, and loads them into a sorted list
   * based on their specified sort order or default positioning.
   *
   * @return mixed Returns an array of configuration module names indexed by their sort order.
   *               If no modules are found, an empty array is returned.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/EditLogError/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\EditLogError\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\EditLogError\EditLogError::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve the configuration information for.
   * @param string $info The specific information to retrieve about the module.
   * @return mixed The requested configuration information of the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('EditLogErrorAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\EditLogError\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('EditLogErrorAdminConfig' . $module, new $class);
    }

    return Registry::get('EditLogErrorAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version, which can be a string or an integer.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier property of the instance.
   *
   * @return string The identifier of the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
