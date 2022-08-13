<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config\SH\Params;

  class free_shipping_over extends \ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public ?int $sort_order = 30;
    public $default = '50';
    public bool $app_configured = true;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_order_total_free_shipping_over_title');
      $this->description = $this->app->getDef('cfg_order_total_free_shipping_over_description');
    }
  }
