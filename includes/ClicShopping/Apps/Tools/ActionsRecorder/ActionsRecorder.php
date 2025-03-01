<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\ActionsRecorder;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class ActionsRecorder extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_ActionsRecorder_V1';

  /**
   * Initializes the necessary components or settings for the current instance.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules within a specified directory and namespace.
   *
   * This method scans a predefined directory for subdirectories containing valid
   * configuration modules. It ensures that these modules extend a specific abstract
   * class and organizes them based on their sort order or their listing order
   * within the directory.
   *
   * @return mixed An array of configuration module names, sorted based on their
   *               sort order or discovery order. Returns an empty array if no
   *               modules are found or an error occurs.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/ActionsRecorder/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\ActionsRecorder\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\ActionsRecorder\ActionsRecorder::getConfigModules(): ';

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
   * @param string $module The name of the module for which to retrieve the configuration information.
   * @param string $info The specific information to retrieve from the module.
   *
   * @return mixed Returns the requested module information, or null if the module or information does not exist.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ActionsRecorderAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\ActionsRecorder\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ActionsRecorderAdminConfig' . $module, new $class);
    }

    return Registry::get('ActionsRecorderAdminConfig' . $module)->$info;
  }

  /**
   * Gets the current API version.
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
