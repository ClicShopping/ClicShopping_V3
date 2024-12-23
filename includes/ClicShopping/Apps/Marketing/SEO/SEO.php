<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\SEO;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class SEO extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_SEO_V1';

  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules available within a specific directory and namespace.
   * The method scans the directory, filters for valid config modules that subclass ConfigAbstract,
   * and sorts them based on their specified sort order.
   *
   * @return mixed An array of configuration module names indexed by their sort order, or an empty array if no modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/SEO/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Marketing\SEO\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Marketing\SEO\SEO::getConfigModules(): ';

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
   * Retrieves specific information about a configuration module.
   *
   * @param string $module The name of the module for which information is being retrieved.
   * @param string $info The specific information to retrieve about the module.
   * @return mixed The requested information for the specified module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('SEOAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Marketing\SEO\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('SEOAdminConfig' . $module, new $class);
    }

    return Registry::get('SEOAdminConfig' . $module)->$info;
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
   * Retrieves the identifier of the current object.
   *
   * @return string The identifier of the object.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
