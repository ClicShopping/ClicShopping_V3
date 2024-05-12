<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class Reviews extends \ClicShopping\OM\AppAbstract
{
  protected $api_version = 1;
  protected string $identifier = 'ClicShopping_Reviews_V1';

  protected function init()
  {
  }

  /**
   * @return array|mixed
   */
  public function getConfigModules(): mixed
  {
    static $result;

    if (!isset($result)) {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Customers/Reviews/Module/ClicShoppingAdmin/Config';
      $name_space_config = 'ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config';
      $trigger_message = 'ClicShopping\Apps\Customers\Reviews\Reviews::getConfigModules(): ';

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
   * @param string $module
   * @param string $info
   * @return mixed
   */
  public function getConfigModuleInfo(string $module, string $info): mixed
  {
    if (!Registry::exists('ReviewsAdminConfig' . $module)) {
      $class = 'ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

      Registry::set('ReviewsAdminConfig' . $module, new $class);
    }

    return Registry::get('ReviewsAdminConfig' . $module)->$info;
  }

  /**
   * @return string|int
   */
  public function getApiVersion(): string|int
  {
    return $this->api_version;
  }

  /**
   * @return string
   */
  public function getIdentifier(): string
  {
    return $this->identifier;
  }
}
