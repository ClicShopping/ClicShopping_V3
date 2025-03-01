<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\Newsletter;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Newsletter extends \ClicShopping\OM\AppAbstract
{

  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Newsletter_V1';

  /**
   * Initializes the necessary configurations or properties for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves a list of configuration modules sorted by their sort order.
   *
   * This method scans a specified directory for configuration modules, checks
   * if they are subclasses of a required abstract class, and stores them in
   * an array, sorted by their defined sort order or the default order if not defined.
   *
   * @return mixed An array of configuration module names, keyed by their sort order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Communication/Newsletter/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Communication\Newsletter\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Communication\Newsletter\Newsletter::getConfigModules(): ';

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
   * Retrieves configuration module information based on the specified module and information type.
   *
   * @param string $module The name of the module to fetch the information for.
   * @param string $info The specific piece of information to retrieve from the module.
   * @return mixed The requested module information, or null if the module or info does not exist.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('NewsletterAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Communication\Newsletter\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('NewsletterAdminConfig' . $module, new $class);
    }

    return Registry::get('NewsletterAdminConfig' . $module)->$info;
  }

  /**
   *
   * @return string|int Returns the current API version.
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
