<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ST\Params;

class private_key extends \ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = '';
  public ?int $sort_order = 40;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_stripe_private_key_title');
    $this->description = $this->app->getDef('cfg_stripe_private_key_desc');
  }
}
