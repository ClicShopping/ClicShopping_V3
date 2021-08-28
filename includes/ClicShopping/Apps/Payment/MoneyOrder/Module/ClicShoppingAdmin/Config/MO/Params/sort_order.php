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

  namespace ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\MO\Params;

  class sort_order extends \ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public ?int $sort_order = 1000;
    public $default = '300';
    public bool $app_configured = false;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_moneyorder_sort_order_title');
      $this->description = $this->app->getDef('cfg_moneyorder_sort_order_description');
    }
  }
