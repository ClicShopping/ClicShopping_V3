<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Zones;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Zones extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Zones_V1';

  /**
   * Initializes the necessary configurations or setups for the current instance.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and initializes configuration modules by scanning a specific directory for available modules.
   * The modules are sorted based on their defined sort order or their appearance in the list.
   *
   * @return mixed Returns an array of configuration module names sorted by their sort order or an empty array if no modules are found.
   *               If the method is executed multiple times, it uses a static cache to avoid reinitialization.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Zones/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\Zones\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\Zones\Zones::getConfigModules(): ';

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
   * Retrieves configuration module information.
   *
   * @param string $module The name of the module to retrieve information from.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed The requested module information.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ZonesAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\Zones\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ZonesAdminConfig' . $module, new $class);
    }

    return Registry::get('ZonesAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the API version.
   *
   * @return string|int The API version.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   *
   * @return string Returns the identifier associated with the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
