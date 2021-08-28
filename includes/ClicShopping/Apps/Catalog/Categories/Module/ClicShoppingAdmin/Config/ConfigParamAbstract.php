<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config;

  use ClicShopping\OM\Registry;

  abstract class ConfigParamAbstract extends \ClicShopping\Sites\ClicShoppingAdmin\ConfigParamAbstract
  {
    protected mixed $app;
    protected $config_module;

    protected string $key_prefix = 'clicshopping_app_categories_';
    public bool $app_configured = true;

    public function __construct($config_module)
    {
      $this->app = Registry::get('Categories');

      $this->key_prefix .= strtolower($config_module) . '_';

      $this->config_module = $config_module;

      $this->code = (new \ReflectionClass($this))->getShortName();

      $this->app->loadDefinitions('Module/ClicShoppingAdmin/Config/' . $config_module . '/Params/' . $this->code);

      parent::__construct();
    }
  }
