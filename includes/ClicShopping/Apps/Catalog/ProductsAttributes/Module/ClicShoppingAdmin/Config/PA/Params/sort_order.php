<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Module\ClicShoppingAdmin\Config\PA\Params;

class sort_order extends \ClicShopping\Apps\Catalog\ProductsAttributes\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = '300';
  public bool $app_configured = false;
  public ?int $sort_order = 300;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_products_attributes_sort_order_title');
    $this->description = $this->app->getDef('cfg_products_attributes_sort_order_description');
  }
}
