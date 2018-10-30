<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config\SH\Params;

  class sort_order extends \ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {

//    public $sort_order = 1000;
    public $default = '500';
    public $app_configured = false;

    protected function init() {
        $this->title = $this->app->getDef('cfg_order_total_shipping_sort_order_title');
        $this->description = $this->app->getDef('cfg_order_total_shipping_sort_order_description');
    }
  }
