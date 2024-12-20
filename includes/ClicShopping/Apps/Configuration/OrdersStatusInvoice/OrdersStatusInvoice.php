<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class OrdersStatusInvoice extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_OrdersStatusInvoice_V1';

  /**
   * Initializes the necessary components or prepares the state for the function or class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules available in a specific directory and namespace.
   * The method scans a predefined directory for configuration modules,
   * validates whether they are subclasses of a designated abstract class,
   * and sorts them based on their sort order property or their natural order.
   *
   * @return mixed An array of configuration module names indexed by their order, or an empty array if no valid modules are found.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/OrdersStatus/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\OrdersStatus\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\OrdersStatus\OrdersStatus::getConfigModules(): ';

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
   * Retrieves configuration module information for a specified module.
   *
   * @param string $module The module name for which configuration information is being retrieved.
   * @param string $info The specific configuration information key to fetch.
   * @return mixed Returns the requested configuration information for the given module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('OrdersStatusInvoiceAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\OrdersStatusInvoice\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('OrdersStatusInvoiceAdminConfig' . $module, new $class);
    }

    return Registry::get('OrdersStatusInvoiceAdminConfig' . $module)->$info;
  }

  /**
   *
   * @return string|int The current API version.
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   *
   * @return string The identifier associated with this instance.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
