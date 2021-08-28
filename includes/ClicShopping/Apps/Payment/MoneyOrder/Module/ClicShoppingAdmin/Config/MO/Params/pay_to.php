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

  class pay_to extends \ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '';
    public ?int $sort_order = 50;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_moneyorder_pay_to_title');
      $this->description = $this->app->getDef('cfg_moneyorder_pay_to_desc');
    }
  }
