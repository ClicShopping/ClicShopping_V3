<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditDesign\Module\ClicShoppingAdmin\Config\ED\Params;

class sort_order extends \ClicShopping\Apps\Tools\EditDesign\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = '300';
  public bool $app_configured = true;
  public ?int $sort_order = 20;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_data_base_tables_sort_order_title');
    $this->description = $this->app->getDef('cfg_data_base_tables_sort_order_description');
  }
}
