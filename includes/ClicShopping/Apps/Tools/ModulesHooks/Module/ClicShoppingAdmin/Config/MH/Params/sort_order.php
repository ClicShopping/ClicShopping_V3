<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\ModulesHooks\Module\ClicShoppingAdmin\Config\MH\Params;

  class sort_order extends \ClicShopping\Apps\Tools\ModulesHooks\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public $default = '300';
    public bool $app_configured = true;
    public ?int $sort_order = 20;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_modules_hooks_sort_order_title');
      $this->description = $this->app->getDef('cfg_modules_hooks_sort_order_description');
    }
  }
