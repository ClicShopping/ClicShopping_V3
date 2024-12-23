<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Cronjob extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Cronjob_V1';

  /**
   * Initializes the necessary components or configurations for the current object.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules for the application, sorted by their predefined sort order
   * or by default index if no sort order is specified. The result is cached for subsequent calls.
   *
   * @return mixed Returns an array of configuration module names, with keys representing their sort order.
   *               If no valid configuration modules are found, an empty array is returned.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/Cronjob/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Tools\Cronjob\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Tools\Cronjob\Cronjob::getConfigModules(): ';

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
   * @param string $info The specific information to retrieve from the module.
   * @return mixed The requested information from the configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('CronjobAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Tools\Cronjob\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('CronjobAdminConfig' . $module, new $class);
    }

    return Registry::get('CronjobAdminConfig' . $module)->$info;
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
