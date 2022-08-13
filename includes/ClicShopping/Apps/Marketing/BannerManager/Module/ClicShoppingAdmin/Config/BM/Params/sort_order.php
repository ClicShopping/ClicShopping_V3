<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\BannerManager\Module\ClicShoppingAdmin\Config\BM\Params;

  class sort_order extends \ClicShopping\Apps\Marketing\BannerManager\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public $default = '30';
    public ?int $sort_order = 300;
    public bool $app_configured = true;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_banner_manager_sort_order_title');
      $this->description = $this->app->getDef('cfg_banner_manager_sort_order_description');
    }
  }
