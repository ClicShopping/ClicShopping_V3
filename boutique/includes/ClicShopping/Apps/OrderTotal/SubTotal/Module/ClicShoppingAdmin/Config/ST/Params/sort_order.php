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

  namespace ClicShopping\Apps\OrderTotal\SubTotal\Module\ClicShoppingAdmin\Config\ST\Params;

  class sort_order extends \ClicShopping\Apps\OrderTotal\SubTotal\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {

//    public $sort_order = 1000;
    public $default = '200';
    public $app_configured = true;

    protected function init() {
        $this->title = $this->app->getDef('cfg_order_total_subtotal_sort_order_title');
        $this->description = $this->app->getDef('cfg_order_total_subtotal_sort_order_description');
    }
  }
