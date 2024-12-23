<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\SubTotal;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class SubTotal extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_SubTotal_V1';

  /**
   * Initializes necessary components or settings for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules available in the specified directory.
   *
   * This function dynamically loads and returns configuration module names
   * that are subclasses of the defined `ConfigAbstract` class within the namespace.
   * The results are sorted by their 'sort_order' property if defined, otherwise
   * they are appended sequentially.
   *
   * @return mixed Returns an array of configuration module names sorted by order,
   *               or an empty array if no modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/OrderTotal/SubTotal/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\OrderTotal\SubTotal\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\OrderTotal\SubTotal\SubTotal::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed Returns the requested configuration information from the module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('SubTotalAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\OrderTotal\SubTotal\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
      Registry::set('SubTotalAdminConfig' . $module, new $class);
    }

    return Registry::get('SubTotalAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The version of the API.
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
