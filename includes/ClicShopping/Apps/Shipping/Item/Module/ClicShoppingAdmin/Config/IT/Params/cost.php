<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config\IT\Params;

class cost extends \ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = '2.50';
  public int|null $sort_order = 40;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_item_cost_title');
    $this->description = $this->app->getDef('cfg_item_cost_desc');
  }
}
