<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Specials;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Specials extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Specials_V1';

  /**
   * Initializes the component or performs initial setup tasks.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Loads and returns the configuration modules for the system. The method scans a specific directory for valid
   * configuration module classes that are subclasses of the required abstract class. Modules are sorted based on
   * their defined sort order or by their discovery order within the directory.
   *
   * @return mixed An array of configuration module filenames indexed by their sort order, or null if no modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Specials/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Marketing\Specials\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Marketing\Specials\Specials::getConfigModules(): ';

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
   * Retrieves specific information for a given configuration module.
   *
   * @param string $module The name of the module to retrieve information for.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed The requested information from the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('SpecialsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Marketing\Specials\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('SpecialsAdminConfig' . $module, new $class);
    }

    return Registry::get('SpecialsAdminConfig' . $module)->$info;
  }

  /**
   *
   * @return string|int The API version.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   *
   * @return string Returns the identifier.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
