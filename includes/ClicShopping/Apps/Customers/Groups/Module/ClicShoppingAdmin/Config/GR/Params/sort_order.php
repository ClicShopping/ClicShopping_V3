<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Module\ClicShoppingAdmin\Config\GR\Params;

class sort_order extends \ClicShopping\Apps\Customers\Groups\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = '30';
  public int|null $sort_order = 300;
  public bool $app_configured = false;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_groups_sort_order_title');
    $this->description = $this->app->getDef('cfg_groups_sort_order_description');
  }
}
