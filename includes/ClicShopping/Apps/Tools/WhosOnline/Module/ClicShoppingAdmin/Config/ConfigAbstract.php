<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config;

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
   * Initializes the class by setting the application and code properties,
   * and calls the initialization method.
   *
   * @return void
   */
  final public function __construct()
  {
    $this->app = Registry::get('WhosOnline');

    $this->code = (new \ReflectionClass($this))->getShortName();

    $this->init();
  }

  /**
   * Installs configuration parameters for the module by iterating through its defined parameters,
   * initializing each with its associated configuration class, and saving these parameters to the application.
   *
   * @return void
   */
  public function install()
  {
    $cut_length = \strlen('CLICSHOPPING_APP_WHOS_ONLINE_' . $this->code . '_');

    foreach ($this->getParameters() as $key) {
      $p = mb_strtolower(substr($key, $cut_length));

      $class = 'ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config\\' . $this->code . '\Params\\' . $p;

      $cfg = new $class($this->code);

      $this->app->saveCfgParam($key, $cfg->default, $cfg->title ?? null, $cfg->description ?? null, $cfg->set_func ?? null);
    }
  }

  /**
   * Removes configuration settings related to the current application from the database.
   *
   * @return int The number of rows affected by the operation.
   */
  public function uninstall()
  {
    $Qdelete = $this->app->db->prepare('delete from :table_configuration
                                          where configuration_key
                                          like :configuration_key
                                          ');
    $Qdelete->bindValue(':configuration_key', 'CLICSHOPPING_APP_WHOS_ONLINE_' . $this->code . '_%');
    $Qdelete->execute();

    return $Qdelete->rowCount();
  }

  /**
   * Retrieves a list of parameter constants specific to the configuration.
   *
   * This function scans a designated directory for parameter files, verifies
   * that each file corresponds to a subclass of the ConfigParamAbstract class,
   * and constructs a list of parameter constants based on valid files found.
   * Invalid files trigger a warning and are not included in the result.
   *
   * @return array An array of parameter constant names related to the configuration.
   */
  public function getParameters()
  {
    $result = [];

    $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/WhosOnline/Module/ClicShoppingAdmin/Config/' . $this->code . '/Params';

    if ($dir = new \DirectoryIterator($directory)) {
      foreach ($dir as $file) {
        if (!$file->isDot() && !$file->isDir() && ($file->getExtension() == 'php')) {
          $class = 'ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config\\' . $this->code . '\\Params\\' . $file->getBasename('.php');

          if (is_subclass_of($class, 'ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config\ConfigParamAbstract')) {
            $result[] = 'CLICSHOPPING_APP_WHOS_ONLINE_' . $this->code . '_' . mb_strtoupper($file->getBasename('.php'));
          } else {
            trigger_error('ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config\\ConfigAbstract::getParameters(): ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config\\' . $this->code . '\\Params\\' . $file->getBasename('.php') . ' is not a subclass of ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config\ConfigParamAbstract and cannot be loaded.');
          }
        }
      }
    }

    return $result;
  }

  /**
   * Processes and retrieves a list of input parameters based on the module's configuration.
   * The method dynamically loads parameter classes, checks their values, and saves configuration
   * parameters if they are not defined, while ensuring proper sort order handling.
   *
   * @return array An associative array of processed input parameters, sorted numerically by their keys.
   */
  public function getInputParameters()
  {
    $result = [];

    $cut = 'CLICSHOPPING_APP_WHOS_ONLINE_' . $this->code . '_';

    $cut_length = \strlen($cut);

    foreach ($this->getParameters() as $key) {
      $p = mb_strtolower(substr($key, $cut_length));

      $class = 'ClicShopping\Apps\Tools\WhosOnline\Module\ClicShoppingAdmin\Config\\' . $this->code . '\Params\\' . $p;

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
