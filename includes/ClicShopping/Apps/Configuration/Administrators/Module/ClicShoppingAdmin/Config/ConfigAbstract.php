<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

abstract class ConfigAbstract
{
  public mixed $app;

  public string $code;
  public $title;
  public string $short_title;
  public string $introduction;
  public array $req_notes = [];
  public bool $is_installed = false;
  public bool $is_uninstallable = false;
  public bool $is_migratable = false;
  public int|null $sort_order = 0;

  abstract protected function init();

  /**
   * Initializes the class by setting up application registry and determining the class name.
   *
   * @return void
   */
  final public function __construct()
  {
    $this->app = Registry::get('Administrators');

    $this->code = (new \ReflectionClass($this))->getShortName();

    $this->init();
  }

  /**
   * Installs the configuration parameters for the current module.
   *
   * @return void
   */
  public function install()
  {
    $cut_length = \strlen('CLICSHOPPING_APP_ADMINISTRATORS_' . $this->code . '_');

    foreach ($this->getParameters() as $key) {
      $p = mb_strtolower(substr($key, $cut_length));

      $class = 'ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\\' . $this->code . '\Params\\' . $p;

      $cfg = new $class($this->code);

      $this->app->saveCfgParam($key, $cfg->default, $cfg->title ?? null, $cfg->description ?? null, $cfg->set_func ?? null);
    }
  }

  /**
   * Uninstalls the application by removing related configuration entries from the database.
   *
   * Prepares and executes an SQL statement to delete records from the configuration table where the configuration key matches a specific pattern.
   *
   * @return int Returns the number of rows affected by the delete operation.
   */
  public function uninstall()
  {
    $Qdelete = $this->app->db->prepare('delete from :table_configuration
                                          where configuration_key
                                          like :configuration_key
                                          ');
    $Qdelete->bindValue(':configuration_key', 'CLICSHOPPING_APP_ADMINISTRATORS_' . $this->code . '_%');
    $Qdelete->execute();

    return $Qdelete->rowCount();
  }

  /**
   * Retrieves a list of configuration parameters for the current module.
   *
   * @return array An array of parameter constants for the given module, formatted as 'CLICSHOPPING_APP_ADMINISTRATORS_<module_code>_<parameter>'.
   *               If a parameter class is not a valid subclass of `ConfigParamAbstract`, an error is triggered.
   */
  public function getParameters()
  {
    $result = [];

    $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Administrators/Module/ClicShoppingAdmin/Config/' . $this->code . '/Params';

    if ($dir = new \DirectoryIterator($directory)) {
      foreach ($dir as $file) {
        if (!$file->isDot() && !$file->isDir() && ($file->getExtension() == 'php')) {
          $class = 'ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\\' . $this->code . '\\Params\\' . $file->getBasename('.php');

          if (is_subclass_of($class, 'ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\ConfigParamAbstract')) {
            $result[] = 'CLICSHOPPING_APP_ADMINISTRATORS_' . $this->code . '_' . mb_strtoupper($file->getBasename('.php'));
          } else {
            trigger_error('ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\\ConfigAbstract::getParameters(): ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\\' . $this->code . '\\Params\\' . $file->getBasename('.php') . ' is not a subclass of ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\ConfigParamAbstract and cannot be loaded.');
          }
        }
      }
    }

    return $result;
  }

  /**
   * Retrieves and processes input parameters for the application configuration.
   * Iterates through parameter keys, applies transformations, and initializes configuration objects
   * with their respective settings. Configured parameters are organized by their sort order and returned.
   *
   * @return array An array of processed input parameters, sorted by configuration order.
   */
  public function getInputParameters()
  {
    $result = [];

    $cut = 'CLICSHOPPING_APP_ADMINISTRATORS_' . $this->code . '_';

    $cut_length = \strlen($cut);

    foreach ($this->getParameters() as $key) {
      $p = mb_strtolower(substr($key, $cut_length));

      $class = 'ClicShopping\Apps\Configuration\Administrators\Module\ClicShoppingAdmin\Config\\' . $this->code . '\Params\\' . $p;

      $cfg = new $class($this->code);


      if (!\defined($key)) {
        $this->app->saveCfgParam($key, $cfg->default, $cfg->title ?? null, $cfg->description ?? null, $cfg->set_func ?? null);
      }

      if ($cfg->app_configured !== false) {
        if (is_numeric($cfg->sort_order)) {
          $counter = (int)$cfg->sort_order;
        } else {
          $counter = \count($result);
        }

        while (true) {
          if (isset($result[$counter])) {
            $counter++;

            continue;
          }

          $set_field = $cfg->getSetField();

          if (!empty($set_field)) {
            $result[$counter] = $set_field;
          }

          break;
        }
      }
    }

    ksort($result, SORT_NUMERIC);

    return $result;
  }
}
