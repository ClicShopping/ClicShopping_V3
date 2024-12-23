<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Recommendations extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Recommendations_V1';

  /**
   * Initializes the object or performs setup tasks.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves configuration modules for the Recommendations app.
   *
   * This method scans the specified directory for configuration module files
   * and verifies if they are subclasses of the ConfigAbstract class.
   * It then organizes them by sort order, or by their position if the sort
   * order is not defined.
   *
   * @return mixed An array of configuration module names sorted by their order, or null if not initialized.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Recommendations/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Marketing\Recommendations\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Marketing\Recommendations\Recommendations::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve information for.
   * @param string $info The specific property or method of the module to access.
   * @return mixed The requested configuration data or functionality from the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('RecommendationsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Marketing\Recommendations\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('RecommendationsAdminConfig' . $module, new $class);
    }

    return Registry::get('RecommendationsAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version as a string or integer.
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
