<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Favorites;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Favorites extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Favorites_V1';

  /**
   * Initializes the required setup or configuration.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules from a specified directory and organizes
   * them based on their sort order or the directory order if no sort order is defined.
   *
   * The method checks for valid configuration classes within the given namespace
   * and ensures they are subclasses of a specific abstract class.
   *
   * @return mixed Returns an array of configuration module names sorted by their
   *               defined sort order or in directory order if no sort order is
   *               provided.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Favorites/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Marketing\Favorites\Favorites::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve configuration for.
   * @param string $info The specific information required from the configuration module.
   * @return mixed The requested information from the specified configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('FavoritesAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('FavoritesAdminConfig' . $module, new $class);
    }

    return Registry::get('FavoritesAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the API version.
   *
   * @return string|int The version of the API.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   *
   * @return string The identifier associated with the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
