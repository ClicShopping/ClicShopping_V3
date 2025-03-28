<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\Stripe;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;
/**
 * Class Stripe is a part of the ClicShopping payment module.
 * It handles the configuration modules and provides access to relevant information regarding payment configurations.
 */
class Stripe extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Stripe_V1';

  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules available within the specified directory.
   *
   * This method scans the predefined directory for available configuration modules, validates
   * them to ensure they extend the required abstract class, and organizes them by sort order.
   * If a configuration module does not specify a sort order, it is appended to the result list.
   * The results are cached in a static variable for efficiency during subsequent calls.
   *
   * @return mixed An array of configuration module names indexed by their sort order,
   *               or an empty array if no modules are found or accessible.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Payment/Stripe/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Payment\Stripe\Stripe::getConfigModules(): ';

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
   * @param string $module The name of the configuration module.
   * @param string $info The specific information to retrieve from the module.
   * @return mixed The requested information from the configuration module.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('StripeAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
      Registry::set('StripeAdminConfig' . $module, new $class);
    }

    return Registry::get('StripeAdminConfig' . $module)->$info;
  }

  /**
   * Retrieves the API version.
   *
   * @return string|int The current API version.
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
