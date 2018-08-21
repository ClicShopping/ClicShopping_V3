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

  namespace ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config\IT\Params;

  class sort_order extends \ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {

    public $default = '300';
    public $app_configured = false;
    public $sort_order = 600;

    protected function init() {
        $this->title = $this->app->getDef('cfg_item_sort_order_title');
        $this->description = $this->app->getDef('cfg_item_sort_order_description');
    }
  }
