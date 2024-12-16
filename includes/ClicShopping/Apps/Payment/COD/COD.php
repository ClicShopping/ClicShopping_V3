<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\COD;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

/**
 * Class COD
 *
 * This class extends the abstract class \ClicShopping\OM\AppAbstract and provides functionalities
 * related to the configuration and management of Cash on Delivery (COD) payment modules within
 * the ClicShopping application. It includes methods for retrieving configuration modules, module
 * information, API version, and identifier.
 */
class COD extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_COD_V1';

  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration module names, ordered by their sort order or default order.
   *
   * The method scans the specific directory for module configurations, checks if they are valid
   * subclasses of the required abstract class, and loads them if appropriate. The resulting list
   * is sorted based on a defined sort order or the default order determined by the scanning process.
   *
   * @return mixed An array of configuration module names indexed by their sort order or default order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Payment/COD/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Payment\COD\COD::getConfigModules(): ';

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
   * @param string $module The name of the module to retrieve the information for.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed The information requested from the configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('CodAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
      Registry::set('CodAdminConfig' . $module, new $class);
    }

    return Registry::get('CodAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version of the system.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier associated with the current instance.
   *
   * @return string The identifier of the instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
