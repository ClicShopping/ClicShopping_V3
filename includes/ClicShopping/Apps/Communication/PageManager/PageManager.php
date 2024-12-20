<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class PageManager extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_PageManager_V1';

  /**
   * Initializes the necessary components or configurations for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves and returns the configuration modules available in a specified directory.
   * The method locates and validates modules that extend `ConfigAbstract` within the namespace,
   * organizing them by their sort order or discovery sequence.
   *
   * @return mixed Returns an array of configuration module filenames indexed by their sort order.
   *               If no modules are found, an empty array is returned.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Communication/PageManager/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Communication\PageManager\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Communication\PageManager\PageManager::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve information for.
   * @param string $info The specific information to retrieve about the module.
   * @return mixed Returns the requested module information, or null if not available.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('PageManagerAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Communication\PageManager\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('PageManagerAdminConfig' . $module, new $class);
    }

    return Registry::get('PageManagerAdminConfig' . $module)->$info;
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
