<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Archive\Module\ClicShoppingAdmin\Config;

use ClicShopping\OM\Registry;

/**
 * Abstract class for managing configuration parameters for a specific module in the ClicShoppingAdmin application.
 */
abstract class ConfigParamAbstract extends \ClicShopping\Sites\ClicShoppingAdmin\ConfigParamAbstract
{
  public mixed $app;
  protected $config_module;

  protected string $key_prefix = 'clicshopping_app_archive_';
  public bool $app_configured = true;

  /**
   * Constructor for initializing the configuration module and setting up dependencies.
   *
   * @param string $config_module The name of the configuration module to initialize.
   * @return void
   */
  public function __construct($config_module)
  {
    $this->app = Registry::get('Archive');

    $this->key_prefix .= mb_strtolower($config_module) . '_';

    $this->config_module = $config_module;

    $this->code = (new \ReflectionClass($this))->getShortName();

    $this->app->loadDefinitions('Module/ClicShoppingAdmin/Config/' . $config_module . '/Params/' . $this->code);

    parent::__construct();
  }
}
