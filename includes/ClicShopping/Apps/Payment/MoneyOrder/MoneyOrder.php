<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\MoneyOrder;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

/**
 * Class MoneyOrder
 *
 * This class represents the MoneyOrder application in the ClicShopping environment.
 * It extends the AppAbstract class and implements the necessary methods to handle
 * the configuration of Money Order payment modules. It provides methods to retrieve
 * configuration modules, module information, API version, and the identifier of the app.
 */

class MoneyOrder extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_MoneyOrder_V1';

  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules from a specified directory. The method scans the directory,
   * identifies valid configuration modules by verifying their inheritance from a specific abstract class,
   * and returns an ordered array of module names based on their sort order or position.
   *
   * @return mixed Returns an array of configuration module names sorted by their defined order or position.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Payment/MoneyOrder/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Payment\MoneyOrder\MoneyOrder::getConfigModules(): ';

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
   * Retrieves configuration module information based on the given module name and information key.
   *
   * @param string $module The name of the module to retrieve information for.
   * @param string $info The specific information key to retrieve from the module.
   * @return mixed The requested module information value.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('MoneyOrderAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
      Registry::set('MoneyOrderAdminConfig' . $module, new $class);
    }

    return Registry::get('MoneyOrderAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the current API version.
   *
   * @return string|int The API version.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * Retrieves the identifier value.
   *
   * @return string The identifier value.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
