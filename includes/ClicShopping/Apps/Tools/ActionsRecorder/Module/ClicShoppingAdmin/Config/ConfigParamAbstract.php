<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\ActionsRecorder\Module\ClicShoppingAdmin\Config;

use ClicShopping\OM\Registry;

abstract class ConfigParamAbstract extends \ClicShopping\Sites\ClicShoppingAdmin\ConfigParamAbstract
{
  public mixed $app;
  protected $config_module;

  protected string $key_prefix = 'clicshopping_app_actions_recorder_';
  public bool $app_configured = true;

  /**
   * Constructor method for initializing the class with the provided configuration module.
   *
   * @param string $config_module The name of the configuration module to be used for initialization.
   * @return void
   */
  public function __construct($config_module)
  {
    $this->app = Registry::get('ActionsRecorder');

    $this->key_prefix .= mb_strtolower($config_module) . '_';

    $this->config_module = $config_module;

    $this->code = (new \ReflectionClass($this))->getShortName();

    $this->app->loadDefinitions('Module/ClicShoppingAdmin/Config/' . $config_module . '/Params/' . $this->code);
    parent::__construct();
  }
}
