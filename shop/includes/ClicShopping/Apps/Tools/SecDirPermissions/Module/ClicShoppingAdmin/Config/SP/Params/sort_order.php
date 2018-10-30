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

  namespace ClicShopping\Apps\Tools\SecDirPermissions\Module\ClicShoppingAdmin\Config\SP\Params;

  class sort_order extends \ClicShopping\Apps\Tools\SecDirPermissions\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {

    public $default = '300';
    public $app_configured = true;
    public $sort_order = 20;

    protected function init() {
        $this->title = $this->app->getDef('cfg_sec_dir_permissions_sort_order_title');
        $this->description = $this->app->getDef('cfg_sec_dir_permissions_sort_order_description');
    }
  }
