<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\MO\Params;

class logo extends \ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = 'cheque.gif';
  public int|null $sort_order = 30;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_moneyorder_title');
    $this->description = $this->app->getDef('cfg_moneyorder_desc');
  }
}
