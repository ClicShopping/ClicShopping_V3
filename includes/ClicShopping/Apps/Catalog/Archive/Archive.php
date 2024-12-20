<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Archive;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

/**
 * Represents the Archive class that extends the AppAbstract class and provides
 * methods for managing configuration modules and retrieving metadata such as
 * API version and instance identifier.
 */
class Archive extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Archive_V1';

  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules and organizes them based on their sort order.
   *
   * @return mixed The sorted list of configuration modules.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Archive/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Catalog\Archive\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Catalog\Archive\Archive::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed The requested module information.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ArchiveAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Catalog\Archive\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ArchiveAdminConfig' . $module, new $class);
    }

    return Registry::get('ArchiveAdminConfig' . $module)->$info;
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
   * Retrieves the identifier of the current instance.
   *
   * @return string The identifier value.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
