<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Langues\Module\ClicShoppingAdmin\Config;

use ClicShopping\OM\Registry;

abstract class ConfigParamAbstract extends \ClicShopping\Sites\ClicShoppingAdmin\ConfigParamAbstract
{
  public mixed $app;
  protected $config_module;

  protected string $key_prefix = 'clicshopping_app_langues_';
  public bool $app_configured = true;

  /**
   * Constructor method for initializing the configuration module.
   *
   * @param string $config_module The configuration module to be initialized.
   * @return void
   */
  public function __construct($config_module)
  {
    $this->app = Registry::get('Langues');

    $this->key_prefix .= mb_strtolower($config_module) . '_';

    $this->config_module = $config_module;

    /**
     *
     */
      $this->code = (new \ReflectionClass($this))->getShortName();

    $this->app->loadDefinitions('Module/ClicShoppingAdmin/Config/' . $config_module . '/Params/' . $this->code);
    parent::__construct();
  }
}
