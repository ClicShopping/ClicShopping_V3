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

  namespace ClicShopping\Apps\Report\StatsLowStock\Module\ClicShoppingAdmin\Config\SL\Params;

  class sort_order extends \ClicShopping\Apps\Report\StatsLowStock\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {

    public $default = '300';
    public $app_configured = false;

    protected function init() {
        $this->title = $this->app->getDef('cfg_stats_low_stock_sort_order_title');
        $this->description = $this->app->getDef('cfg_stats_low_stock_sort_order_description');
    }
  }
