<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TemplateEmail;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class TemplateEmail extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_TemplateEmail_V1';

  /**
   * Initializes the required configurations or sets up initial states for the class.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Retrieves the configuration modules by scanning a specific directory
   * for valid module classes which are subclasses of ConfigAbstract.
   * Modules are sorted by their defined sort order or by insertion order.
   *
   * @return mixed Returns an array of configuration module names indexed by their sort order.
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/TemplateEmail/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Configuration\TemplateEmail\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Configuration\TemplateEmail\TemplateEmail::getConfigModules(): ';

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
   * Retrieves configuration module information for the specified module and info key.
   *
   * @param string $module The name of the module to retrieve information for.
   * @param string $info The specific information key to retrieve from the module configuration.
   * @return mixed Returns the requested module configuration information, or null if not found.
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('TemplateEmailAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Configuration\TemplateEmail\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('TemplateEmailAdminConfig' . $module, new $class);
    }

    return Registry::get('TemplateEmailAdminConfig' . $module)->$info;
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
   *
   * @return string The identifier value.
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
